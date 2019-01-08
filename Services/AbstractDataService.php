<?php

namespace FatchipAfterbuy\Services;

use Psr\Log\LoggerInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\CachedConfigReader;

class AbstractDataService {

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ModelManager
     */
    protected $entityManager;

    protected $config;

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
    }

    public function registerAPINamespaces(string $path) {
        Shopware()->Container()->get('loader')->registerNamespace(
            'Fatchip\Afterbuy',
            $path . '/Library/API/'
        );
    }
}