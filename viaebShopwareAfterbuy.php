<?php

namespace viaebShopwareAfterbuy;

use viaebShopwareAfterbuy\Models\Status;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Shopware-Plugin FatchipAfterbuy.
 */
class viaebShopwareAfterbuy extends Plugin
{
    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('viaeb_shopware_afterbuy.plugin_dir', $this->getPath());
        $container->setParameter('viaeb_shopware_afterbuy.plugin_name', $this->getName());

        parent::build($container);
    }

    /**
     * @param InstallContext $context
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function install(InstallContext $context)
    {
        parent::install($context);

        $service = $this->container->get('shopware_attribute.crud_service');
        $service->update('s_categories_attributes', 'afterbuy_catalog_id', 'string');

        $service->update('s_order_attributes', 'afterbuy_order_id', 'string', [
            'label' => 'Afterbuy OrderId',
            'displayInBackend' => true
        ]);

        $service->update('s_articles_attributes', 'afterbuy_parent_id', 'string');
        $service->update('s_articles_attributes', 'afterbuy_id', 'string');

        $service->update('s_articles_attributes', 'afterbuy_export_enabled', 'boolean', [
            'label' => 'Artikel zu Afterbuy exportieren',
            'supportText' => 'Wenn "Alle Artikel exportieren" in den Plugineinstellungen deaktiviert ist, werden nur Artikel exportiert, für die diese Funktionalität explizit gesetzt wurde',
            'displayInBackend' => true,
        ]);

        Shopware()->Models()->generateAttributeModels(['s_categories_attributes', 's_order_attributes', 's_articles_attributes']);

        $em = $this->container->get('models');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = [$em->getClassMetadata(Status::class)];

        $tableNames = array('afterbuy_status');

        $schemaManager = Shopware()->Container()->get('models')->getConnection()->getSchemaManager();
        if (!$schemaManager->tablesExist($tableNames)) {
            $tool->createSchema($classes);

            $status = new Status();
            $status->setId(1);
            $status->setLastProductExport(new \DateTime('1970-01-01'));
            $status->setLastProductImport(new \DateTime('1970-01-01'));
            $status->setLastOrderImport(new \DateTime('1970-01-01'));
            $status->setLastStatusExport(new \DateTime('1970-01-01'));

            $em->persist($status);
            $em->flush();
        }
    }

    public function uninstall(UninstallContext $context)
    {
        if($context->keepUserData() !== true) {
            $this->deleteAttributes();
            $this->deleteSchema();
        }
    }

    public function deleteAttributes() {
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->delete('s_categories_attributes', 'afterbuy_catalog_id');
        $service->delete('s_order_attributes', 'afterbuy_order_id');
        $service->delete('s_articles_attributes', 'afterbuy_parent_id');
        $service->delete('s_articles_attributes', 'afterbuy_id');
        $service->delete('s_articles_attributes', 'afterbuy_export_enabled');

        Shopware()->Models()->generateAttributeModels(['s_categories_attributes', 's_order_attributes', 's_articles_attributes']);
    }

    public function deleteSchema() {
        $em = $this->container->get('models');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = [$em->getClassMetadata(Status::class)];

        $tableNames = array('afterbuy_status');

        $schemaManager = Shopware()->Container()->get('models')->getConnection()->getSchemaManager();
        if ($schemaManager->tablesExist($tableNames)) {
            $tool->dropSchema($classes);
        }
    }
}