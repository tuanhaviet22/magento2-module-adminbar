<?php
declare(strict_types=1);

namespace TH\Adminbar\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Cms\Api\Data\PageInterface;
use TH\Adminbar\Helper\Data as AdminbarHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\Cms\Model\Page as CmsPage;

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
        // Try multiple possible registry keys for CMS pages
        $cmsPage = $this->registry->registry('cms_page');
        if (!$cmsPage) {
            $cmsPage = $this->registry->registry('current_cms_page');
        }
        if (!$cmsPage) {
            $cmsPage = $this->registry->registry('current_page');
        }

        // If still not found, try to detect CMS page by request
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
            // Check if we're on a CMS page by looking at the request
            $moduleName = $this->request->getModuleName();
            $controllerName = $this->request->getControllerName();
            $actionName = $this->request->getActionName();

            // CMS pages typically use cms/page/view or cms/index/index
            if ($moduleName === 'cms' &&
                (($controllerName === 'page' && $actionName === 'view') ||
                 ($controllerName === 'index' && $actionName === 'index'))) {

                // Get page identifier from request
                $pageId = $this->request->getParam('page_id');
                $identifier = $this->request->getParam('id');

                if ($pageId) {
                    // Load by page ID
                    return $this->pageRepository->getById($pageId);
                } elseif ($identifier) {
                    // Load by identifier
                    $storeId = $this->_storeManager->getStore()->getId();
                    return $this->pageRepository->getByIdentifier($identifier, $storeId);
                }
            }
        } catch (\Exception $e) {
            // Silently fail and return null
        }

        return null;
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
            // Get admin frontName from deployment config
            $adminFrontName = $this->getAdminFrontName();
            if (!$adminFrontName) {
                // Fallback to default if frontName not found
                return $this->urlBuilder->getUrl($route, $params);
            }

            // Build admin URL with dynamic frontName
            $baseUrl = $this->urlBuilder->getBaseUrl();
            $routePath = ltrim($route, '/');

            // Build query string if params exist
            $queryString = '';
            if (!empty($params)) {
                $queryString = '?' . http_build_query($params);
            }

            // Construct full admin URL: https://app.bebe9.test/admin_1i2pgp/admin/dashboard/
            return rtrim($baseUrl, '/') . '/' . $adminFrontName . '/' . $routePath . '/' . $queryString;

        } catch (\Exception $e) {
            // Fallback to standard URL generation if something fails
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
        } catch (\Exception $e) {
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
