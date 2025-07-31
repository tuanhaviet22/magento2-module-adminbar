<?php
/*
 *  @author    TuanHa
 *  @copyright Copyright (c) 2025 Tuan Ha <https://www.tuanha.dev/>
 *
 */

declare(strict_types=1);

namespace TH\Adminbar\Block;

use Exception;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\Page as CmsPage;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;
use TH\Adminbar\Helper\Data as AdminbarHelper;

/**
 * Admin Bar Block
 */
class Adminbar extends Template
{
    /**
     * @var AdminSession
     */
    private $adminSession;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var AdminbarHelper
     */
    private $adminbarHelper;

    /**
     * @param Context $context
     * @param AdminSession $adminSession
     * @param AppState $appState
     * @param Registry $registry
     * @param UrlInterface $urlBuilder
     * @param PageRepositoryInterface $pageRepository
     * @param DeploymentConfig $deploymentConfig
     * @param RequestInterface $request
     * @param AdminbarHelper $adminbarHelper
     * @param CustomerSession $customerSession
     * @param UserContextInterface $userContext
     * @param array $data
     */
    public function __construct(
        Context $context,
        AdminSession $adminSession,
        AppState $appState,
        Registry $registry,
        UrlInterface $urlBuilder,
        PageRepositoryInterface $pageRepository,
        DeploymentConfig $deploymentConfig,
        RequestInterface $request,
        AdminbarHelper $adminbarHelper,
        CustomerSession $customerSession = null,
        UserContextInterface $userContext = null,
        array $data = []
    ) {
        $this->adminSession = $adminSession;
        $this->appState = $appState;
        $this->registry = $registry;
        $this->urlBuilder = $urlBuilder;
        $this->pageRepository = $pageRepository;
        $this->deploymentConfig = $deploymentConfig;
        $this->request = $request;
        $this->adminbarHelper = $adminbarHelper;
        $this->customerSession = $customerSession ?: ObjectManager::getInstance()->get(CustomerSession::class);
        $this->userContext = $userContext ?: ObjectManager::getInstance()->get(UserContextInterface::class);
        parent::__construct($context, $data);
    }

    /**
     * Check if the admin bar should be displayed
     * Note: This only checks frontend configuration, actual admin session is checked via AJAX
     *
     * @return bool
     */
    public function shouldDisplay(): bool
    {
        return $this->adminbarHelper->shouldShow();
    }

    /**
     * Get admin bar helper
     *
     * @return AdminbarHelper
     */
    public function getAdminbarHelper(): AdminbarHelper
    {
        return $this->adminbarHelper;
    }

    /**
     * Get current product if on product page
     *
     * @return Product|null
     */
    public function getCurrentProduct(): ?Product
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get current category if on category page
     *
     * @return Category|null
     */
    public function getCurrentCategory(): ?Category
    {
        return $this->registry->registry('current_category');
    }

    /**
     * Get current CMS page if on CMS page
     *
     * @return CmsPage|null
     */
    public function getCurrentCmsPage(): ?CmsPage
    {
        $cmsPage = $this->registry->registry('cms_page');
        if (!$cmsPage) {
            $cmsPage = $this->registry->registry('current_cms_page');
        }
        if (!$cmsPage) {
            $cmsPage = $this->registry->registry('current_page');
        }

        if (!$cmsPage) {
            $cmsPage = $this->detectCmsPageFromRequest();
        }

        return $cmsPage;
    }

    /**
     * Detect CMS page from current request
     *
     * @return CmsPage|null
     */
    private function detectCmsPageFromRequest(): ?CmsPage
    {
        try {
            $moduleName = $this->request->getModuleName();
            $controllerName = $this->request->getControllerName();
            $actionName = $this->request->getActionName();

            if ($moduleName === 'cms' &&
                (($controllerName === 'page' && $actionName === 'view') ||
                    ($controllerName === 'index' && $actionName === 'index'))) {

                $pageId = $this->request->getParam('page_id');
                $identifier = $this->request->getParam('id');

                if ($pageId) {
                    return $this->pageRepository->getById($pageId);
                } elseif ($identifier) {
                    $storeId = $this->_storeManager->getStore()->getId();
                    return $this->pageRepository->getByIdentifier($identifier, $storeId);
                }
            }
        } catch (Exception $e) {
        }

        return null;
    }

    /**
     * Get current order if on order view page
     *
     * @return \Magento\Sales\Model\Order|null
     */
    public function getCurrentOrder(): ?Order
    {
        return $this->registry->registry('current_order');
    }

    /**
     * Get current invoice if on invoice view page
     *
     * @return \Magento\Sales\Model\Order\Invoice|null
     */
    public function getCurrentInvoice(): ?Invoice
    {
        return $this->registry->registry('current_invoice');
    }

    /**
     * Get current shipment if on shipment view page
     *
     * @return \Magento\Sales\Model\Order\Shipment|null
     */
    public function getCurrentShipment(): ?Shipment
    {
        return $this->registry->registry('current_shipment');
    }

    /**
     * Get admin URL for viewing current order
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderViewUrl(int $orderId): string
    {
        return $this->getAdminUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * Get admin URL for viewing current invoice
     *
     * @param int $invoiceId
     * @return string
     */
    public function getInvoiceViewUrl(int $invoiceId): string
    {
        return $this->getAdminUrl('sales/invoice/view', ['invoice_id' => $invoiceId]);
    }

    /**
     * Get admin URL for viewing current shipment
     *
     * @param int $shipmentId
     * @return string
     */
    public function getShipmentViewUrl(int $shipmentId): string
    {
        return $this->getAdminUrl('sales/shipment/view', ['shipment_id' => $shipmentId]);
    }

    /**
     * Get admin URL for editing current product
     *
     * @param int $productId
     * @return string
     */
    public function getProductEditUrl(int $productId): string
    {
        return $this->getAdminUrl('catalog/product/edit', ['id' => $productId]);
    }

    /**
     * Get admin URL for editing current category
     *
     * @param int $categoryId
     * @return string
     */
    public function getCategoryEditUrl(int $categoryId): string
    {
        return $this->getAdminUrl('catalog/category/edit', ['id' => $categoryId]);
    }

    /**
     * Get admin URL for editing current CMS page
     *
     * @param int $pageId
     * @return string
     */
    public function getCmsPageEditUrl(int $pageId): string
    {
        return $this->getAdminUrl('cms/page/edit', ['page_id' => $pageId]);
    }

    /**
     * Get admin URL for editing current customer
     *
     * @param int $customerId
     * @return string
     */
    public function getCustomerEditUrl($customerId)
    {
        return $this->getAdminUrl('customer/index/edit', ['id' => $customerId]);
    }

    /**
     * Get orders listing URL in admin
     *
     * @return string
     */
    public function getOrdersUrl()
    {
        return $this->getAdminUrl('sales/order');
    }

    /**
     * Get invoices listing URL in admin
     *
     * @return string
     */
    public function getInvoicesUrl()
    {
        return $this->getAdminUrl('sales/invoice');
    }

    /**
     * Get shipments listing URL in admin
     *
     * @return string
     */
    public function getShipmentsUrl()
    {
        return $this->getAdminUrl('sales/shipment');
    }

    /**
     * Get credit memos listing URL in admin
     *
     * @return string
     */
    public function getCreditmemosUrl()
    {
        return $this->getAdminUrl('sales/creditmemo');
    }

    /**
     * Get products listing URL in admin
     *
     * @return string
     */
    public function getProductsUrl()
    {
        return $this->getAdminUrl('catalog/product');
    }

    /**
     * Get categories listing URL in admin
     *
     * @return string
     */
    public function getCategoriesUrl()
    {
        return $this->getAdminUrl('catalog/category');
    }

    /**
     * Get customers listing URL in admin
     *
     * @return string
     */
    public function getCustomersUrl()
    {
        return $this->getAdminUrl('customer/index');
    }

    /**
     * Get system configuration URL in admin
     *
     * @return string
     */
    public function getConfigUrl()
    {
        return $this->getAdminUrl('admin/system_config');
    }

    /**
     * Get admin dashboard URL
     *
     * @return string
     */
    public function getAdminDashboardUrl(): string
    {
        return $this->getAdminUrl('admin/dashboard');
    }

    /**
     * Get cache management URL
     *
     * @return string
     */
    public function getCacheManagementUrl(): string
    {
        return $this->getAdminUrl('admin/cache');
    }

    /**
     * Get indexer management URL
     *
     * @return string
     */
    public function getIndexerManagementUrl(): string
    {
        return $this->getAdminUrl('indexer/indexer/list');
    }

    /**
     * Get admin URL for given path and parameters
     * Uses dynamic admin frontName from backend configuration
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    private function getAdminUrl(string $route, array $params = []): string
    {
        try {
            $adminFrontName = $this->getAdminFrontName();
            if (!$adminFrontName) {
                return $this->urlBuilder->getUrl($route, $params);
            }

            $baseUrl = $this->urlBuilder->getBaseUrl();
            $routePath = ltrim($route, '/');

            $queryString = '';
            if (!empty($params)) {
                $queryString = '?' . http_build_query($params);
            }

            return rtrim($baseUrl, '/') . '/' . $adminFrontName . '/' . $routePath . '/' . $queryString;

        } catch (Exception $e) {
            return $this->urlBuilder->getUrl($route, $params);
        }
    }

    /**
     * Get admin frontName from deployment configuration
     *
     * @return string|null
     */
    private function getAdminFrontName(): ?string
    {
        try {
            $backendConfig = $this->deploymentConfig->get('backend');
            return $backendConfig['frontName'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get admin session
     *
     * @return AdminSession
     */
    public function getAdminSession(): AdminSession
    {
        return $this->adminSession;
    }

    /**
     * Get current customer if logged in
     *
     * @return \Magento\Customer\Model\Customer|null
     */
    public function getCurrentCustomer()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer();
        }
        return null;
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get debug information for troubleshooting
     *
     * @return array
     */
    public function getDebugInfo(): array
    {
        return [
            'module' => $this->request->getModuleName(),
            'controller' => $this->request->getControllerName(),
            'action' => $this->request->getActionName(),
            'page_id' => $this->request->getParam('page_id'),
            'identifier' => $this->request->getParam('id'),
            'cms_page_registry' => $this->registry->registry('cms_page') ? 'found' : 'not_found',
            'current_cms_page_registry' => $this->registry->registry('current_cms_page') ? 'found' : 'not_found',
            'current_page_registry' => $this->registry->registry('current_page') ? 'found' : 'not_found',
        ];
    }
}
