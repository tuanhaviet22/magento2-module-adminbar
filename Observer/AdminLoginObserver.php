<?php
declare(strict_types=1);

namespace TH\Adminbar\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\App\Config\ScopeConfigInterface;

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
        } catch (\Exception $e) {
            // Silently fail to avoid breaking admin login
            // Log error if needed
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

            // Check if we should refresh the cookie
            if (!$this->shouldRefreshCookie()) {
                return;
            }

            // Create secure admin cookie data with fresh timestamp
            $cookieData = [
                'logged_in' => true,
                'user_id' => $user->getId(),
                'username' => $user->getUsername(),
                'timestamp' => time(), // Fresh timestamp extends the cookie
                'last_activity' => time(),
                // Add a hash for security
                'hash' => hash('sha256', $user->getId() . $user->getUsername() . time())
            ];

            // Encode cookie data
            $cookieValue = base64_encode(json_encode($cookieData));

            // Set cookie metadata with admin session lifetime
            $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
            $publicCookieMetadata->setDuration($this->getAdminSessionLifetime());
            $publicCookieMetadata->setPath('/');
            $publicCookieMetadata->setHttpOnly(false); // Allow JavaScript access
            $publicCookieMetadata->setSecure(false); // Set to true in production with HTTPS
            $publicCookieMetadata->setSameSite('Lax');

            // Set the admin cookie with fresh expiration
            $this->cookieManager->setPublicCookie(
                'th_admin_bar_auth',
                $cookieValue,
                $publicCookieMetadata
            );

            // Also store in session as backup
            $this->sessionManager->setData('th_admin_bar_auth', $cookieData);

        } catch (\Exception $e) {
            // Silently fail
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
            // Get current cookie
            $cookieValue = $this->cookieManager->getCookie('th_admin_bar_auth');
            if (!$cookieValue) {
                return true; // No cookie exists, create one
            }

            // Decode existing cookie
            $decodedData = base64_decode($cookieValue);
            if (!$decodedData) {
                return true; // Invalid cookie, refresh it
            }

            $cookieData = json_decode($decodedData, true);
            if (!is_array($cookieData) || !isset($cookieData['timestamp'])) {
                return true; // Invalid data, refresh it
            }

            // Only refresh if cookie is older than refresh threshold
            // Refresh every 5 minutes (300 seconds) to avoid too frequent updates
            $refreshThreshold = 300;
            $timeSinceLastUpdate = time() - $cookieData['timestamp'];

            return $timeSinceLastUpdate >= $refreshThreshold;

        } catch (\Exception $e) {
            return true; // On error, refresh the cookie
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
