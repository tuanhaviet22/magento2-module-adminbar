<?php
declare(strict_types=1);

namespace TH\Adminbar\Controller\Adminhtml\Auth;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use TH\Adminbar\Observer\AdminLoginObserver;

/**
 * Admin controller to refresh admin cookie
 * This endpoint is called from frontend to extend cookie lifetime
 */
class Refresh extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Backend::admin';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var AdminLoginObserver
     */
    private $adminLoginObserver;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param AdminLoginObserver $adminLoginObserver
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        AdminLoginObserver $adminLoginObserver
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->adminLoginObserver = $adminLoginObserver;
        parent::__construct($context);
    }

    /**
     * Refresh admin cookie if admin is still logged in
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        
        try {
            // Refresh the admin cookie
            $this->adminLoginObserver->refreshAdminCookie();
            
            return $resultJson->setData([
                'success' => true,
                'message' => 'Cookie refreshed successfully'
            ]);
            
        } catch (\Exception $e) {
            return $resultJson->setData([
                'success' => false,
                'message' => 'Failed to refresh cookie'
            ]);
        }
    }

    /**
     * Check if user has access to this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        // Allow any admin user to refresh their own cookie
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
