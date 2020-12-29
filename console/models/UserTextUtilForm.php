<?php

namespace backend\models\forms;


use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class UserTextUtilForm extends Model
{
    const TYPE_COMMA = 'comma';
    const TYPE_SEMICOLON = 'semicolon';

    const MODE_REPLACEDATES = 'replaceDates';
    const MODE_COUNTAVERAGE = 'countAverageLineCount';

    public $delimiter;
    public $mode;
    /**
     * @var UploadedFile
     */
    public $csvFile;

    /**
     * @var $filePath string Путь к файлу на сервере (не url)
     */
    public $filePath;

    const FOLDER_CSV = 'csv';

    /**
     * @var array
     */
    private $responseArray = [];

    public function rules()
    {
        return [
            [['csvFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'csv', 'checkExtensionByMimeType'=>false],
        ];
    }

    public static function delimiterTypes()
    {
        return [
            self::TYPE_COMMA  => '.',
            self::TYPE_SEMICOLON => ';',
        ];
    }

    /**
     * Загрузить csv файл
     * @return bool
     * @throws \yii\base\Exception
     */
    public function process()
    {
        if (!$this->validate()) {
            return false;
        }
        if (!$this->setDelimiter()) {
            return false;
        }

        $uploadDir = \Yii::getAlias('@filesUploads') . '/' . self::FOLDER_CSV;
        if (!file_exists($uploadDir)) {
            FileHelper::createDirectory($uploadDir);
        }

        $fileName = \Yii::$app->security->generateRandomString() . '.' . $this->csvFile->extension;
        $filePath = $uploadDir . '/' . $fileName;

        $this->filePath = $filePath;

        Yii::trace($filePath);

        $this->csvFile->saveAs($filePath);

        $csvArr = $this->parseCsv();
        unlink($filePath);
        if (empty($csvArr)) {
            return false;
        }

        if (!$this->saveInDb($csvArr)) {
            return false;
        }

        return true;
    }


    /**
     * Прочитать csv файл и получить таблицу в виде двумерного массива
     * @return array|bool
     */
    public function parseCsv()
    {
        $filePath = $this->filePath;

        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->addError('', 'Не удалось найти файл');
            return false;
        }

        $rawStr = file_get_contents($filePath);
        $utf8str = EncodingHelper::convertToUtf8($rawStr);
        $linesArr = explode(PHP_EOL, $utf8str);
        if (empty($linesArr)) {
            $this->addError('', 'Файл пустой');
            return false;
        }


        $resultArr = [];
        $cellsCount = 0;// Количество столбцов

        foreach ($linesArr as $line) {

            $row = str_getcsv($line, $this->delimitter);
            $currentCellsCount = count($row); // Количество столбцов в текущей строке

            if ($currentCellsCount > $cellsCount) {
                $cellsCount = $currentCellsCount;
            }

            if ($currentCellsCount < 10) {
                continue;
            }

            $resultArr[] = $row;

        }

        $rowsCount = count($resultArr); // Количество строк

        \Yii::trace("Получен массив из csv файла размером: {$rowsCount} x {$cellsCount}");

        if ($rowsCount < 1) {
            $this->addError('', 'Файл пустой');
            return false;
        }

        return $resultArr;
    }


    /**
     * Установить разделитель csv файла
     */
    private function setDelimiter()
    {
        $this->delimiter = self::delimiterTypes()[$this->delimiter];
        if (empty($this->delimiter)) {
            $this->addError('', 'Не найден указанный разделитель');
            return false;
        }

        return true;
    }


    /**
     * Сохранить данные из таблицы в виде двумерного массива в базу данных
     * @param $arr
     * @return bool
     * @throws \yii\db\Exception
     */
    private function saveInDb($arr)
    {
        if (empty($arr)) {
            return false;
        }

        $productCount = 0;

        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        $skipHeader = true;


        foreach ($arr as $row) {
            if (count($row) < 2) {
                continue;
            }
            if ($skipHeader) {
                $skipHeader = false;
                continue;
            }

            $product = new Product();
            $product->name = $row[0];
            $product->categoryId = $this->categoryId;
            $product->companyId = $this->companyId;
            $product->inStock = Product::IS_IN_STOCK;
            $product->price = (float)$row[2];
            $product->productDescription = $row[3];
            $product->calories = !empty($row[4]) ? (float)$row[4] : null;
            $product->proteins = !empty($row[5]) ? (float)$row[5] : null;
            $product->fat = !empty($row[6]) ? (float)$row[6] : null;
            $product->carbohydrates = !empty($row[7]) ? (float)$row[7] : null;
            $product->weight = (float)$row[8];
            $product->unitType = !empty($row[9]) ? self::unitTypes()[$row[9]] : 1;
            if ($product->save()) {
                $productCount++;
                $model = new ProductImage();
                $model->productId = $product->productId;
                $binaryImage = CurlComponent::sendJsonRequest($row[1]);
                /**
                 * @var File $file
                 */
                $file = new File();
                if ($file->saveBinaryImage($binaryImage, ProductImage::FOLDER_PRODUCT_ICONS)) {
                    $model->fileId = $file->fileId;
                    $model->save();
                }
            }
        }

        $transaction->commit();

        $message = "Добавлено товаров: {$productCount}";

        Yii::trace($message);
        Yii::$app->session->setFlash('success', $message);

        return true;
    }

    public function getSuccessResponse()
    {
        switch ($this->mode) {
            case self::MODE_COUNTAVERAGE:
                $text = 'Average lines result:' . PHP_EOL;
                break;
            case self::MODE_REPLACEDATES:
                $text = 'Replaced dates count:' . PHP_EOL;
                break;
            default:
                $text = '';
                break;
        }

        foreach ($this->responseArray as $key => $value) {
            $text .= "User: {$key}. count:{$value};" . PHP_EOL;
        }

        return $text;
    }
}