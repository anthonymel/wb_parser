<?php
namespace console\controllers;

use common\components\Parser;
use Yii;
use yii\console\Controller;
use console\models\UserTextUtilForm;

class FileController extends Controller
{
    public function actionParseCatalog()
    {
        /** @var Parser $dataParse */
        $dataParse = Yii::$app->parser->loadCategories();
        if (empty($dataParse)) {
            $this->stdout('fail parse');
        }
        $categoryArr = json_decode($dataParse);
        foreach ($categoryArr as $category) {
            if ($category->shard == "men_clothes") {
                $this->parseChild($category);
            }
        }
        $this->stdout("the end");
    }

    public function parseChild($category)
    {
        foreach ($category->childs as $childItem) {
            $productResult = json_decode(Yii::$app->parser->loadProductList($childItem->shard, $childItem->query));
            foreach ($productResult->data->products as $product) {
                $currentPrice = Parser::getCurrentPrice($product->salePriceU, $product->priceU);
                $lastPrice = Yii::$app->parser->getLastPrice($product->id);
                if ($currentPrice < $lastPrice * 0.5) {
                    $this->stdout("https://www.wildberries.ru/catalog/$product->id/detail.aspx?targetUrl=GP" . "\n");
                }
            }
            if (!empty($childItem->childs)) {
                $this->parseChild($childItem);
            }
        }
    }

    public function actionParseProduct()
    {
        /** @var Parser $dataParse */
        $dataParse = Yii::$app->parser->loadProductPriceHistory(37503888);
        if (empty($dataParse)) {
            $this->stdout('fail parse');
        }
        $priceArr = json_decode($dataParse);
        foreach ($priceArr as $priceItem) {
            $this->stdout(Yii::$app->formatter->asDate($priceItem->dt) . " " . (int)$priceItem->price->RUB / 100 . "\n");
        }
        die;
    }
}
