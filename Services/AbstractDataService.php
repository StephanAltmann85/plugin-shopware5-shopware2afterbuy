<?php

namespace FatchipAfterbuy\Services;

use FatchipAfterbuy\Services\Helper\AbstractHelper;
use Psr\Log\LoggerInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\CachedConfigReader;
use Symfony\Component\Intl\NumberFormatter\NumberFormatter;

class AbstractDataService {

    /**
     * @var string $identifier
     */
    protected $identifier;

    /**
     * @var bool $isAttribute
     */
    protected $isAttribute;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ModelManager
     */
    protected $entityManager;

    protected $config;

    public $apiConfig;

    /**
     * @var AbstractHelper $helper
     */
    public $helper;

     /**
     * provides the target entity (valueObject) given via services.xml
     * !!! if different services etc are needed, we will make use of factories (symfony) !!!
     *
     * AbstractReadDataService constructor.
     * @param string $targetEntity
     * @param ModelManager $entityManager
     */
    public function __construct(ModelManager $entityManager = null) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function setConfig(CachedConfigReader $configReader, string $pluginName) {
        $this->config = $configReader->getByPluginName($pluginName);

        $this->apiConfig = [
            'afterbuyAbiUrl'               => 'https://api.afterbuy.de/afterbuy/ABInterface.aspx',
            'afterbuyShopInterfaceBaseUrl' => 'https://api.afterbuy.de/afterbuy/ShopInterfaceUTF8.aspx',
            'afterbuyPartnerId'            => $this->config["partnerId"],
            'afterbuyPartnerPassword'      => $this->config["partnerPassword"],
            'afterbuyUsername'             => $this->config["userName"],
            'afterbuyUserPassword'         => $this->config["userPassword"],
            'logLevel'                     => '1',
        ];
    }

    public function registerAPINamespaces(string $path) {
        Shopware()->Container()->get('loader')->registerNamespace(
            'Fatchip\Afterbuy',
            $path . '/Library/API/'
        );
    }

    /**
     * @param AbstractHelper $helper
     * @param string $identifier
     * @param bool $isAttribute
     */
    public function initHelper(AbstractHelper $helper, string $identifier, bool $isAttribute) {
        $this->helper = $helper;
        $this->identifier = $identifier;
        $this->isAttribute = $isAttribute;
    }
}