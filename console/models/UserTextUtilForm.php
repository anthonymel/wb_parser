<?php

namespace console\models;


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

    /**
     * @var array
     */
    private $usersArray = [];


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

        if (!$this->parseCsv()) {
            return false;
        }

        switch ($this->mode) {
            case self::MODE_COUNTAVERAGE:
                $this->countAvgLines();
                break;
            case self::MODE_REPLACEDATES:
                $this->replaceDates();
                break;
            default:
                $this->addError('','Unknown mode selected');
                break;
        }


        return true;
    }




    /**
     * Загрузка пользователей в массив
     * @return array|bool
     */
    public function parseCsv()
    {
        $filePath = "console/files/people.csv";

        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->addError('', "Can't open file");
            return false;
        }

        $handle = fopen($filePath, "r");
        while (!feof($handle)) {
            $buffer = fgets($handle, 4096);
            $userInfo = explode($this->delimiter, $buffer);
            $this->usersArray[$userInfo[0]] = $userInfo[1];
        }
    }


    /**
     * Установить разделитель csv файла
     */
    private function setDelimiter()
    {
        $this->delimiter = self::delimiterTypes()[$this->delimiter];
        if (empty($this->delimiter)) {
            $this->addError('', 'Unknown delimiter');
            return false;
        }

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