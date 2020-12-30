<?php

namespace console\models;


use yii\base\Model;
use DateTime;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class UserTextUtilForm extends Model
{
    const TYPE_COMMA = 'comma';
    const TYPE_SEMICOLON = 'semicolon';

    const MODE_REPLACEDATES = 'replaceDates';
    const MODE_COUNTAVERAGE = 'countAverageLineCount';

    const MAIN_FOLDER = 'console/files';

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
                return $this->countAvgLines();
                break;
            case self::MODE_REPLACEDATES:
                return $this->replaceDates();
                break;
            default:
                $this->addError('','Unknown mode selected');
                return false;
                break;
        }
    }

    private function checkFolder($folder)
    {
        $files = scandir($folder, 1);
        return !$files ? false : true;
    }

    private function countAvgLines()
    {
        if (!$this->checkFolder(self::MAIN_FOLDER . "/texts")) {
            $this->addError('', "Can't find text files");
            return false;
        }

        foreach ($this->usersArray as $id => $name) {
            $linesCount = 0;
            $files = glob(self::MAIN_FOLDER . "/texts/{$id}-*.txt");
            foreach ($files as $file) {
                $linesCount += count(file($file));
            }
            $this->responseArray[$name] = $linesCount / count($files);
        }
        return true;
    }

    private function replaceDates()
    {
        if (!$this->checkFolder(self::MAIN_FOLDER . "/texts")) {
            $this->addError('', "Can't find text files");
            return false;
        }
        foreach ($this->usersArray as $id => $name) {
            $counter = 0;
            $files = glob(self::MAIN_FOLDER . "/texts/{$id}-*.txt");
            foreach ($files as $file) {
                $string = (file_get_contents($file));
                preg_match_all('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2}/', $string, $dates);
                foreach ($dates[0] as $date) {
                    $formattedDate = explode('/', $date);
                    $dateTime = $formattedDate[1] . '-' . $formattedDate[0] . '-' . '20' . $formattedDate[2];
                    $string = str_replace($date, $dateTime, $string);
                    $counter++;
                }
                file_put_contents(str_replace('texts', 'output_texts', $file), $string);
            }
            $this->responseArray[$name] = $counter;
        }
        return true;
    }

    /**
     * Загрузка пользователей в массив
     * @return array|bool
     */
    private function parseCsv()
    {
        $filePath = self::MAIN_FOLDER . "/people.csv";
        if (!file_exists($filePath) || !is_readable($filePath) || !$this->checkFolder(self::MAIN_FOLDER)) {
            $this->addError('', "Can't open file");
            return false;
        }

        $handle = fopen($filePath, "r");
        while (!feof($handle)) {
            $buffer = fgets($handle, 4096);
            $userInfo = explode($this->delimiter, $buffer);
            if (count($userInfo) < 2) {
                $this->addError('', 'Empty data');
                return false;
            }
            $this->usersArray[trim($userInfo[0])] = trim($userInfo[1]);
        }

        return true;
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

        foreach ($this->responseArray as $name => $count) {
            $text .= "User: {$name}. count: {$count};" . PHP_EOL;
        }

        return $text;
    }
}