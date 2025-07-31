<?php
/*
 *  @author    TuanHa
 *  @copyright Copyright (c) 2025 Tuan Ha <https://www.tuanha.dev/>
 *
 */

declare(strict_types=1);

namespace TH\Adminbar\Helper;

use Exception;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Store\Model\ScopeInterface;

/**
 * Admin Bar Helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'th_adminbar/general/enabled';
    const XML_PATH_SHOW_IN_PRODUCTION = 'th_adminbar/general/show_in_production';

    /**
     * @var State
     */
    private $appState;

    /**
     * @var AdminSession
     */
    private $adminSession;

    /**
     * @param Context $context
     * @param State $appState
     * @param AdminSession $adminSession
     */
    public function __construct(
        Context $context,
        State $appState,
        AdminSession $adminSession
    ) {
        $this->appState = $appState;
        $this->adminSession = $adminSession;
        parent::__construct($context);
    }

    /**
     * Check if admin bar is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if admin bar should be shown (frontend context)
     * Note: Admin session check is done via AJAX call for security
     *
     * @param int|null $storeId
     * @return bool
     */
    public function shouldShow(?int $storeId = null): bool
    {
        if (!$this->isEnabled($storeId)) {
            return false;
        }

        try {
            $mode = $this->appState->getMode();
            if ($mode === State::MODE_PRODUCTION) {
                return $this->scopeConfig->isSetFlag(
                    self::XML_PATH_SHOW_IN_PRODUCTION,
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                );
            } else {
                return true;
            }
        } catch (Exception $e) {
            return true;
        }

        return true;
    }

    /**
     * Check if admin bar should be shown (admin context)
     * This method is used in admin controllers where admin session is available
     *
     * @param int|null $storeId
     * @return bool
     */
    public function shouldShowForAdmin(?int $storeId = null): bool
    {
        if (!$this->isEnabled($storeId)) {
            return false;
        }

        if (!$this->adminSession->isLoggedIn()) {
            return false;
        }

        try {
            $mode = $this->appState->getMode();
            if ($mode === State::MODE_PRODUCTION) {
                return $this->scopeConfig->isSetFlag(
                    self::XML_PATH_SHOW_IN_PRODUCTION,
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                );
            }
        } catch (Exception $e) {
            return true;
        }

        return true;
    }
}
