<?php
namespace Werules\MultistoreProductUrls\Plugin;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Cms\Controller\Noroute\Index as NoRouteAction;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;

class NoRouteLogger
{
    private $logger;
    private $resultRedirectFactory;
    private $storeManager;
    private $urlFinder;
    private $cookieManager;
    private $cookieMetadataFactory;
    private $sessionManager;

    public function __construct(
        LoggerInterface $logger,
        RedirectFactory $resultRedirectFactory,
        StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager
    ) {
        $this->logger = $logger;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->storeManager = $storeManager;
        $this->urlFinder = $urlFinder;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
    }

    public function afterExecute(NoRouteAction $subject, $result)
    {
//        $this->logger->debug("NoRouteLogger: afterExecute");
        $request = $subject->getRequest();

        if ($request->getParam('werules_switch_handled')) {
            return $result;
        }

        $request->setParam('werules_switch_handled', true);

        $requestedPath = $request->getPathInfo();
        $trimmedPath = ltrim($requestedPath, '/');

        foreach ($this->storeManager->getStores() as $store) {
            $storeId = (int)$store->getId();

            $rewrite = $this->urlFinder->findOneByData([
                'request_path' => $trimmedPath,
                'store_id'     => $storeId
            ]);

            if ($rewrite) {
                $currentStoreId = (int)$this->storeManager->getStore()->getId();
                if ($storeId === $currentStoreId) {
                    continue;
                }

                // (Optional) Switch the store context for this request
                $this->storeManager->setCurrentStore($storeId);

                // Persist the new store in the session
                $this->sessionManager->setData('store', $store->getCode());

                // Persist the new store in cookies
                $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                    ->setDuration(3600 * 24 * 365) // 1 year
                    ->setPath('/')
                    ->setHttpOnly(false)
                    ->setSameSite('lax');
                $this->cookieManager->setPublicCookie('store', $store->getCode(), $cookieMetadata);

                // Construct redirect URL with store code and safety param
                $baseUrl = rtrim($store->getBaseUrl(), '/');
                $redirectUrl = $baseUrl . '/' . $trimmedPath;

                /** @var \Magento\Framework\Controller\Result\Redirect $redirectResult */
                $redirectResult = $this->resultRedirectFactory->create();
                $redirectResult->setUrl($redirectUrl);

                return $redirectResult;
            }
        }

        return $result;
    }
}
