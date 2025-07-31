<?php
/*
 *  @author    TuanHa
 *  @copyright Copyright (c) 2025 Tuan Ha <https://www.tuanha.dev/>
 *
 */

declare(strict_types=1);

namespace TH\Adminbar\Observer;

use Exception;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Observer to track admin login for admin bar functionality
 */
class AdminLoginObserver implements ObserverInterface
{
    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var AdminSession
     */
    private $adminSession;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param SessionManagerInterface $sessionManager
     * @param AdminSession $adminSession
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        SessionManagerInterface $sessionManager,
        AdminSession $adminSession,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->sessionManager = $sessionManager;
        $this->adminSession = $adminSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Set admin cookie when admin logs in or refresh if still logged in
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            if ($this->adminSession->isLoggedIn() && $this->adminSession->getUser()) {
                $this->refreshAdminCookie();
            }
        } catch (Exception $e) {

        }
    }

    /**
     * Refresh admin cookie with updated timestamp
     * This extends the cookie lifetime if admin is still active
     *
     * @return void
     */
    public function refreshAdminCookie(): void
    {
        try {
            $user = $this->adminSession->getUser();
            if (!$user) {
                return;
            }
            if (!$this->shouldRefreshCookie()) {
                return;
            }
            $cookieData = [
                'logged_in' => true,
                'user_id' => $user->getId(),
                'username' => $user->getUsername(),
                'timestamp' => time(),
                'last_activity' => time(),
                'hash' => hash('sha256', $user->getId() . $user->getUsername() . time())
            ];
            $cookieValue = base64_encode(json_encode($cookieData));
            $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
            $publicCookieMetadata->setDuration($this->getAdminSessionLifetime());
            $publicCookieMetadata->setPath('/');
            $publicCookieMetadata->setHttpOnly(false);
            $publicCookieMetadata->setSecure(false);
            $publicCookieMetadata->setSameSite('Lax');

            $this->cookieManager->setPublicCookie(
                'th_admin_bar_auth',
                $cookieValue,
                $publicCookieMetadata
            );

            $this->sessionManager->setData('th_admin_bar_auth', $cookieData);

        } catch (Exception $e) {

        }
    }

    /**
     * Check if cookie should be refreshed
     * Only refresh if cookie is older than refresh threshold
     *
     * @return bool
     */
    private function shouldRefreshCookie(): bool
    {
        try {

            $cookieValue = $this->cookieManager->getCookie('th_admin_bar_auth');
            if (!$cookieValue) {
                return true;
            }
            $decodedData = base64_decode($cookieValue);
            if (!$decodedData) {
                return true;
            }
            $cookieData = json_decode($decodedData, true);
            if (!is_array($cookieData) || !isset($cookieData['timestamp'])) {
                return true;
            }
            $refreshThreshold = 300;
            $timeSinceLastUpdate = time() - $cookieData['timestamp'];
            return $timeSinceLastUpdate >= $refreshThreshold;
        } catch (Exception $e) {
            return true;
        }
    }

    /**
     * Get admin session lifetime from configuration
     *
     * @return int
     */
    private function getAdminSessionLifetime(): int
    {
        try {
            $sessionLifetime = $this->scopeConfig->getValue(
                'admin/security/session_lifetime',
                ScopeInterface::SCOPE_STORE
            );
            return (int)$sessionLifetime ?: 7200;
        } catch (Exception $e) {
            return 7200;
        }
    }
}
