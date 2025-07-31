<?php
/*
 *  @author    TuanHa
 *  @copyright Copyright (c) 2025 Tuan Ha <https://www.tuanha.dev/>
 *
 */

declare(strict_types=1);

namespace TH\Adminbar\Controller\Adminhtml\Auth;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Session\SessionManagerInterface;
use TH\Adminbar\Helper\Data as AdminbarHelper;

/**
 * Admin authentication status controller (Admin context)
 * This controller runs in admin context where admin session is available
 */
class Status extends Action implements HttpGetActionInterface
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
     * @var AdminSession
     */
    private $adminSession;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var AdminbarHelper
     */
    private $adminbarHelper;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param AdminSession $adminSession
     * @param SessionManagerInterface $sessionManager
     * @param AdminbarHelper $adminbarHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        AdminSession $adminSession,
        SessionManagerInterface $sessionManager,
        AdminbarHelper $adminbarHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->adminSession = $adminSession;
        $this->sessionManager = $sessionManager;
        $this->adminbarHelper = $adminbarHelper;
        parent::__construct($context);
    }

    /**
     * Check if admin is logged in and admin bar should be shown
     * This method has access to admin session
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        try {
            $isLoggedIn = $this->adminSession->isLoggedIn();
            $shouldShow = $this->adminbarHelper->shouldShowForAdmin();

            $response = [
                'success' => true,
                'isLoggedIn' => $isLoggedIn,
                'shouldShow' => $shouldShow,
                'userId' => null,
                'userName' => null
            ];

            if ($isLoggedIn && $this->adminSession->getUser()) {
                $user = $this->adminSession->getUser();
                $response['userId'] = $user->getId();
                $response['userName'] = $user->getUsername();

                // Update session data for frontend access
                $this->sessionManager->setData('th_admin_bar_auth', [
                    'logged_in' => true,
                    'last_check' => time(),
                    'user_info' => [
                        'userId' => $user->getId(),
                        'userName' => $user->getUsername()
                    ]
                ]);
            }

            return $resultJson->setData($response);

        } catch (Exception $e) {
            return $resultJson->setData([
                'success' => false,
                'isLoggedIn' => false,
                'shouldShow' => false,
                'error' => 'Authentication check failed'
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
        // Allow any admin user to check their own status
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
