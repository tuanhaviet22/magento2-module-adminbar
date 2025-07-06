<?php
declare(strict_types=1);

namespace TH\Adminbar\Controller\Auth;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\UrlInterface;
use TH\Adminbar\Helper\Data as AdminbarHelper;

/**
 * Frontend controller to refresh admin cookie
 * Makes a request to admin endpoint to refresh cookie if admin is still logged in
 */
class Refresh implements ActionInterface, HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var AdminbarHelper
     */
    private $adminbarHelper;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param Curl $curl
     * @param DeploymentConfig $deploymentConfig
     * @param UrlInterface $urlBuilder
     * @param AdminbarHelper $adminbarHelper
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        Curl $curl,
        DeploymentConfig $deploymentConfig,
        UrlInterface $urlBuilder,
        AdminbarHelper $adminbarHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->curl = $curl;
        $this->deploymentConfig = $deploymentConfig;
        $this->urlBuilder = $urlBuilder;
        $this->adminbarHelper = $adminbarHelper;
    }

    /**
     * Refresh admin cookie by making request to admin endpoint
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        
        try {
            // Get admin frontName from deployment config
            $adminFrontName = $this->getAdminFrontName();
            if (!$adminFrontName) {
                return $resultJson->setData([
                    'success' => false,
                    'message' => 'Admin frontName not found'
                ]);
            }

            // Build admin refresh URL
            $baseUrl = $this->urlBuilder->getBaseUrl();
            $adminUrl = rtrim($baseUrl, '/') . '/' . $adminFrontName . '/thadminbar/auth/refresh/';
            
            // Make internal request to admin endpoint with current cookies
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_TIMEOUT, 5);
            $this->curl->setOption(CURLOPT_FOLLOWLOCATION, true);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $this->curl->setOption(CURLOPT_COOKIE, $this->getCurrentCookies());
            
            $this->curl->post($adminUrl, []);
            $response = $this->curl->getBody();
            $httpCode = $this->curl->getStatus();
            
            if ($httpCode === 200 && $response) {
                $adminResponse = json_decode($response, true);
                if ($adminResponse && isset($adminResponse['success'])) {
                    return $resultJson->setData($adminResponse);
                }
            }
            
            return $resultJson->setData([
                'success' => false,
                'message' => 'Failed to refresh admin session'
            ]);
            
        } catch (\Exception $e) {
            return $resultJson->setData([
                'success' => false,
                'message' => 'Refresh failed: ' . $e->getMessage()
            ]);
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
     * Get current cookies for the request
     *
     * @return string
     */
    private function getCurrentCookies(): string
    {
        $cookies = [];
        foreach ($_COOKIE as $name => $value) {
            $cookies[] = $name . '=' . $value;
        }
        return implode('; ', $cookies);
    }
}
