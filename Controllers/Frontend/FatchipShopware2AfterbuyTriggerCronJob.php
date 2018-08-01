<?php

use \Shopware\FatchipShopware2Afterbuy\Components\ImportProductsCronJob;

/**
 * Frontend controller
 */
class Shopware_Controllers_Frontend_FatchipShopware2AfterbuyTriggerCronJob extends
    Enlight_Controller_Action {
    public function triggerAction() {
        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();

        $importProductsCronJob = new ImportProductsCronJob();
        $importProductsCronJob->importProducts2Shopware();
    }
}
