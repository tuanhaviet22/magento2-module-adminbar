<?php
declare(strict_types=1);

namespace TH\Adminbar\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use TH\Adminbar\Observer\AdminLoginObserver;

/**
 * Observer to track admin activity and refresh cookie
 * Triggers on admin controller actions to extend cookie lifetime
 */
class AdminActivityObserver implements ObserverInterface
{
    /**
     * @var AdminLoginObserver
     */
    private $adminLoginObserver;

    /**
     * @param AdminLoginObserver $adminLoginObserver
     */
    public function __construct(
        AdminLoginObserver $adminLoginObserver
    ) {
        $this->adminLoginObserver = $adminLoginObserver;
    }

    /**
     * Refresh admin cookie on admin activity
     * This extends the cookie lifetime for active admin users
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            // Refresh the admin cookie to extend its lifetime
            $this->adminLoginObserver->refreshAdminCookie();
        } catch (\Exception $e) {
            // Silently fail to avoid breaking admin functionality
        }
    }
}
