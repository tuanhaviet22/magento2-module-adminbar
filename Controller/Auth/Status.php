<?php
declare(strict_types=1);

namespace TH\Adminbar\Controller\Auth;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use TH\Adminbar\Helper\Data as AdminbarHelper;

/**
 * Frontend controller for admin authentication status
 * Uses secure cookie-based approach to check admin authentication
 */
class Status implements ActionInterface, HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var AdminbarHelper
     */
    private $adminbarHelper;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param CookieManagerInterface $cookieManager
     * @param ScopeConfigInterface $scopeConfig
     * @param AdminbarHelper $adminbarHelper
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        CookieManagerInterface $cookieManager,
        ScopeConfigInterface $scopeConfig,
        AdminbarHelper $adminbarHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cookieManager = $cookieManager;
        $this->scopeConfig = $scopeConfig;
        $this->adminbarHelper = $adminbarHelper;
    }

    /**
     * Check admin authentication status using secure cookie
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        // First check if admin bar is enabled in frontend context
        if (!$this->adminbarHelper->shouldShow()) {
            return $resultJson->setData([
                'success' => true,
                'isLoggedIn' => false,
                'shouldShow' => false,
                'message' => 'Admin bar is disabled'
            ]);
        }

        try {
            // Check admin authentication via cookie
            $adminData = $this->getAdminDataFromCookie();

            if ($adminData && $this->isValidAdminData($adminData)) {
                return $resultJson->setData([
                    'success' => true,
                    'isLoggedIn' => true,
                    'shouldShow' => true,
                    'userId' => $adminData['user_id'] ?? null,
                    'userName' => $adminData['username'] ?? null
                ]);
            }

            // No valid admin session found
            return $resultJson->setData([
                'success' => true,
                'isLoggedIn' => false,
                'shouldShow' => false,
                'message' => 'Admin session not found'
            ]);

        } catch (\Exception $e) {
            return $resultJson->setData([
                'success' => false,
                'isLoggedIn' => false,
                'shouldShow' => false,
                'error' => 'Authentication check failed'
            ]);
        }
    }

    /**
     * Get admin data from cookie
     *
     * @return array|null
     */
    private function getAdminDataFromCookie(): ?array
    {
        try {
            $cookieValue = $this->cookieManager->getCookie('th_admin_bar_auth');
            if (!$cookieValue) {
                return null;
            }

            $decodedData = base64_decode($cookieValue);
            if (!$decodedData) {
                return null;
            }

            $adminData = json_decode($decodedData, true);
            return is_array($adminData) ? $adminData : null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validate admin data from cookie
     *
     * @param array $adminData
     * @return bool
     */
    private function isValidAdminData(array $adminData): bool
    {
        // Check required fields
        if (!isset($adminData['logged_in'], $adminData['user_id'], $adminData['username'], $adminData['timestamp'])) {
            return false;
        }

        // Check if logged in
        if (!$adminData['logged_in']) {
            return false;
        }

        // Check if not expired using admin session lifetime
        $currentTime = time();
        $cookieTime = $adminData['timestamp'];
        $sessionLifetime = $this->getAdminSessionLifetime();

        if (($currentTime - $cookieTime) > $sessionLifetime) {
            return false;
        }

        return true;
    }

    /**
     * Get admin session lifetime from configuration
     *
     * @return int
     */
    private function getAdminSessionLifetime(): int
    {
        try {
            // Get admin session lifetime from configuration
            // Path: admin/security/session_lifetime
            $sessionLifetime = $this->scopeConfig->getValue(
                'admin/security/session_lifetime',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            // Default to 7200 seconds (2 hours) if not configured
            return (int)$sessionLifetime ?: 7200;

        } catch (\Exception $e) {
            // Default fallback: 2 hours
            return 7200;
        }
    }
}
