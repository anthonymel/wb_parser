<?php
namespace console\controllers;

use common\components\Parser;
use Longman\TelegramBot\Request;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\console\Controller;
use console\models\UserTextUtilForm;
use Longman\TelegramBot\Telegram;

class FileController extends Controller
{
    private $sentProductIds = [];

    public function actionParseCatalog($categoryName)
    {
        /** @var Parser $dataParse */
        $dataParse = Yii::$app->parser->loadCategories();
        if (empty($dataParse)) {
            $this->stdout('fail parse');
        }
        $categoryArr = json_decode($dataParse);
        foreach ($categoryArr as $category) {
            if (!empty($category->childs) && $category->shard == $categoryName) {
                $this->parseChild($category);
            }
        }
        $this->stdout("the end");
    }

    public function parseChild($category)
    {
        foreach ($category->childs as $childItem) {
            try {
                $this->stdout($childItem->name . "\n");
                for ($page = 1; $page <= \Yii::$app->params['limitPages']; $page++) {
                    $productResult = json_decode(Yii::$app->parser->loadProductList($childItem->shard, $childItem->query, $page));
                    if (empty($productResult)) {
                        continue;
                    }
                    foreach ($productResult->data->products as $product) {
                        if (in_array($product->id, $this->sentProductIds)) {
                            continue;
                        }
                        $this->stdout('▮');
                        $currentPrice = Parser::getCurrentPrice($product->salePriceU, $product->priceU);
                        $lastPrice = Yii::$app->parser->getLastPrice($product->id);
                        if (/*$currentPrice < \Yii::$app->params['minTargetPrice'] ||*/ $currentPrice < $lastPrice * \Yii::$app->params['targetPercent'] || $currentPrice < $product->salePriceU * \Yii::$app->params['targetPercent']) {
                            $this->stdout("\n $product->id \n");
                            if ($this->checkQuantity($product)) {
                                $substr = Parser::prepareProductString($product->id);
                                $message = $product->name . " 🤩 всего за " . $currentPrice / 100 . " руб \n";
                                $message .= "🔥 🔥 🔥" . "\n";
                                $message .= "[⁠](https://images.wbstatic.net/c246x328/new/{$substr}/$product->id-1.jpg)" . "\n";
                                $message .= "https://www.wildberries.ru/catalog/$product->id/detail.aspx?targetUrl=GP" . "\n";
                                $this->sendMessageToChat($message);
                                $this->sentProductIds[] = $product->id;
                                $this->stdout('✔');
                            }
                        }
                    }
                }

                if (!empty($childItem->childs)) {
                    $this->parseChild($childItem);
                }
            } catch (ErrorException $exception) {
                $this->stdout("parsing error $childItem->name\n");
                $this->stdout($exception->getMessage());
                continue;
            }
        }
    }

    public function sendMessageToChat($message)
    {
        $botToken = Yii::$app->params['tgBotToken'];
        $chat_id =  Yii::$app->params['tgChatId'];
        $bot_url    = "https://api.telegram.org/bot$botToken/";
        $url = $bot_url."sendMessage?chat_id=".$chat_id."&parse_mode=markdown&text=".urlencode($message);
        file_get_contents($url);
    }

    public function checkQuantity($product)
    {
        $details = json_decode(\Yii::$app->parser->loadProductDetails($product->id));
        if (empty($details->data) || empty($details->data->products[0]->sizes)) {
            return false;
        }

        $qnty = 0;
        foreach ($details->data->products[0]->sizes as $size) {

            if (empty($size->stocks)) {
                continue;
            }
            foreach ($size->stocks as $stock) {
                $qnty += $stock->qty;
            }
        }

        return $qnty > 0;
    }
}
