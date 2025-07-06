<?php
declare(strict_types=1);

namespace TH\Adminbar\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;

/**
 * Observer to track admin logout for admin bar functionality
 */
class AdminLogoutObserver implements ObserverInterface
{
    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @param SessionManagerInterface $sessionManager
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        SessionManagerInterface $sessionManager,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->sessionManager = $sessionManager;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Clear admin cookie and session data when admin logs out
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            // Clear admin authentication data from session
            $this->sessionManager->unsetData('th_admin_bar_auth');

            // Delete the admin cookie
            $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
            $cookieMetadata->setPath('/');

            $this->cookieManager->deleteCookie(
                'th_admin_bar_auth',
                $cookieMetadata
            );
        } catch (\Exception $e) {
            // Silently fail to avoid breaking admin logout
        }
    }
}
