<?php

namespace common\components;

use Yii;
use \yii\base\BaseObject;
use yii\base\Component;

class Parser extends Component
{
    public $categoryUrl;
    public $productUrl;
    public $productUrlv2;
    public $productListUrl;
    public $priceHistoryUrl;
    public $host;
    public $curlOpt;

     public function loadCategories()
    {
        return $this->sendGetRequest($this->categoryUrl);
    }

    public function loadProductDetails($productId)
    {
        return $this->sendGetRequest($this->productUrlv2 . $productId);
    }

    public function loadProductList($shard, $query)
    {
        return $this->sendGetRequest($this->productListUrl . "$shard/catalog?lang=ru&locale=ru&reg=0&$query");
    }

    public function loadProductPriceHistory($productId)
    {
        return $this->sendGetRequest($this->priceHistoryUrl . $productId . '.json');
    }

    public function getLastPrice($productId)
    {
        $historyObjects = json_decode($this->loadProductPriceHistory($productId));
        return !is_null($historyObjects) ? $historyObjects[array_key_last($historyObjects)]->price->RUB : 500;
    }

    public function sendGetRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlOpt('header'));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getCurlOpt('userAgent'));
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);
        if (curl_exec($ch) === false) {
            throw new \Exception(curl_errno($ch) . ': ' . curl_error($ch) . $url);
        }
        curl_close($ch);

        Yii::info(Yii::t('app', 'Loading data page'));

        return $result;
    }


    protected function getCurlOpt($nameOpt)
    {
        if ($nameOpt !== 'userAgent' && $nameOpt !== 'header') {
            return false;
        }
        return $this->curlOpt[$nameOpt];
    }

    /**
     * Получение текущей цены в RUB
     * @param $productId
     *
     * @return int|mixed
     */
    public static function getCurrentPrice($salePriceU, $priceU)
    {
        return min([
            (int)$salePriceU,
            (int)$priceU
        ]);
    }
}