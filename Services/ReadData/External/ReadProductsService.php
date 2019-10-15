<?php
/** @noinspection SpellCheckingInspection */

namespace viaebShopwareAfterbuy\Services\ReadData\External;

use Fatchip\Afterbuy\ApiClient;
use viaebShopwareAfterbuy\Components\Helper;
use viaebShopwareAfterbuy\Services\Helper\AfterbuyProductsHelper;
use viaebShopwareAfterbuy\Services\ReadData\AbstractReadDataService;
use viaebShopwareAfterbuy\Services\ReadData\ReadDataInterface;
use viaebShopwareAfterbuy\ValueObjects\Article as ValueArticle;

class ReadProductsService extends AbstractReadDataService implements ReadDataInterface
{

    /**
     * @param array $filter
     *
     * @return ValueArticle[]
     */
    public function get(array $filter)
    {
        $data = $this->read($filter);

        return $this->transform($data);
    }

    /**
     * transforms api input into ValueArticle (targetEntity)
     * TODO: refactor
     * @param array $products
     *
     * @return ValueArticle[]
     */
    public function transform(array $products)
    {
        $this->logger->debug('Receiving products from afterbuy', $products);

         if ($this->targetEntity === null) {
            return array();
        }

        /** @var ValueArticle[] $valueArticles */
        $valueArticles = array();

        foreach ($products as $product) {

            if (empty($product)) {
                continue;
            }

            /** @var ValueArticle $valueArticle */
            $valueArticle = new $this->targetEntity();

            $valueArticle->setExternalIdentifier($product['ProductID']);
            $valueArticle->setAnr($product['Anr']);

            /** TODO: move + refactor start */
            switch ((int)$this->config['ordernumberMapping']) {
                case 0:
                    $valueArticle->setOrdernunmber($valueArticle->getExternalIdentifier());
                    break;
                case 1:
                    $valueArticle->setOrdernunmber($valueArticle->getAnr());
                    if (
                        !$valueArticle->getOrdernunmber()
                        || $valueArticle->getOrdernunmber() === 0
                        || $valueArticle->getOrdernunmber() === '0'
                    ) {
                        continue 2;
                    }
                    break;
            }
            /** TODO: move end */

            $valueArticle->setEan($product['EAN']);
            $valueArticle->setName($product['Name']);
            $valueArticle->setPrice(Helper::convertDeString2Float($product['SellingPrice']));
            $valueArticle->setManufacturer($product['ProductBrand']);
            $valueArticle->setStock($product['Quantity']);
            $valueArticle->setStockMin((int)$product['MinimumStock']);
            $valueArticle->setTax(Helper::convertDeString2Float($product['TaxRate']));
            $valueArticle->setDescription($product['Description']);
            $valueArticle->setUnitOfQuantity($product['UnitOfQuantity']);
            $valueArticle->setBasePriceFactor($product['BasepriceFactor']);
            $valueArticle->setWeight($product['Weight']);
            $valueArticle->setSupplierNumber($product['ManufacturerPartNumber']);
            $valueArticle->setDiscontinued($product['Discontinued']);

            $valueArticle->setFree1(key_exists('FreeValue1', $product) ? $product['FreeValue1'] : '');
            $valueArticle->setFree2(key_exists('FreeValue2', $product) ? $product['FreeValue2'] : '');
            $valueArticle->setFree3(key_exists('FreeValue3', $product) ? $product['FreeValue3'] : '');
            $valueArticle->setFree4(key_exists('FreeValue4', $product) ? $product['FreeValue4'] : '');
            $valueArticle->setFree5(key_exists('FreeValue5', $product) ? $product['FreeValue5'] : '');
            $valueArticle->setFree6(key_exists('FreeValue6', $product) ? $product['FreeValue6'] : '');
            $valueArticle->setFree7(key_exists('FreeValue7', $product) ? $product['FreeValue7'] : '');
            $valueArticle->setFree8(key_exists('FreeValue8', $product) ? $product['FreeValue8'] : '');
            $valueArticle->setFree9(key_exists('FreeValue9', $product) ? $product['FreeValue9'] : '');
            $valueArticle->setFree10(key_exists('FreeValue10', $product) ? $product['FreeValue10'] : '');

            /** @var AfterbuyProductsHelper $helper */
            $helper = $this->helper;
            $helper->addProductPictures($product, $valueArticle);

            /** TODO: move start */
            // catalogs - categories
            if (array_key_exists('Catalogs', $product) && array_key_exists('CatalogID', $product['Catalogs'])) {
                $catalogIDs = $product['Catalogs']['CatalogID'];
                if ( ! is_array($catalogIDs)) {
                    $catalogIDs = [$catalogIDs];
                }

                $valueArticle->setExternalCategoryIds($catalogIDs);
            }
            /** TODO: move end */

            if ((int)$product['Quantity'] > (int)$product['MinimumStock'] && Helper::convertDeString2Float($product['SellingPrice'] > 0)) {
                $valueArticle->setActive(true);
            }

            /** TODO: move start */
            $variants = [];

            if (!array_key_exists('Attributes', $product) && array_key_exists('BaseProducts', $product) && $product['BaseProductFlag'] !== '1'
                && array_key_exists('BaseProductID', $product['BaseProducts']['BaseProduct'])) {
                $valueArticle->setMainArticleId($product['BaseProducts']['BaseProduct']['BaseProductID']);

                $variants[] = array(
                    'option' => 'Variation',
                    'value'  => $product['Name'],
                );
            }

            if (array_key_exists('Attributes', $product) && array_key_exists('BaseProducts', $product) && $product['BaseProductFlag'] !== '1'
                && array_key_exists('BaseProductID', $product['BaseProducts']['BaseProduct'])) {
                $valueArticle->setMainArticleId($product['BaseProducts']['BaseProduct']['BaseProductID']);


                if (array_key_exists('AttributName', $product['Attributes']['Attribut'])) {
                    $variants[] = array(
                        'option' => $product['Attributes']['Attribut']['AttributName'],
                        'value'  => $product['Attributes']['Attribut']['AttributValue'],
                    );
                } else {
                    $variants = [];

                    foreach ($product['Attributes']['Attribut'] as $option) {
                        $variant = array(
                            'option' => $option['AttributName'],
                            'value'  => $option['AttributValue'],
                        );

                        $variants[] = $variant;
                    }
                }
            }
            /** TODO: move end */

            /** TODO: move start */
            if (
                key_exists('BaseProductFlag', $product) and $product['BaseProductFlag'] !== '1'
                or !key_exists('BaseProductFlag', $product)
            ) {
                $helper->readAttributes($valueArticle, $product);
            }

            if ( ! empty($variants) && $product['BaseProductFlag'] !== '1') {
                $valueArticle->setVariants($variants);
            }
            /** TODO: move end */

            if(!$valueArticle->getMainArticleId()) {
                $valueArticles[] = $valueArticle;
            }
            else {
                array_unshift($valueArticles, $valueArticle);
            }
        }

        return $valueArticles;
    }


    /**
     * provides api data. dummy data as used here can be used in tests
     *
     * @param array $filter
     *
     * @return array
     */
    public function read(array $filter)
    {

        $resource = new ApiClient($this->apiConfig, $this->logger);
        $data = $resource->getAllShopProductsFromAfterbuy($filter);

        if ( ! $data || empty($data)) {
            return array();
        }

        return $data;
    }
}