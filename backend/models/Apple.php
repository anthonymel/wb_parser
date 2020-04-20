<?php

namespace backend\models;

/**
 * This is the model class for table "apple".
 *
 * @property int $appleId
 * @property string $color
 * @property string $volume
 * @property int $status
 * @property int $droppedAt
 * @property int $createdAt
 * @property int $updatedAt
 */
class Apple extends \yii\db\ActiveRecord
{
    const STATUS_ON_TREE = 0;
    const STATUS_DROPPED = 1;
    const STATUS_ROTTEN = 2;

    const STATUS_ON_TREE_LABEL = 'На дереве';
    const STATUS_DROPPED_LABEL = 'Упало';
    const STATUS_ROTTEN_LABEL = 'Упало';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['color'], 'required'],
            [['volume'], 'number'],
            [['status', 'droppedAt', 'createdAt', 'updatedAt'], 'integer'],
            [['status'], 'integer', 'min' => self::STATUS_ON_TREE, 'max' => self::STATUS_DROPPED],
            [['color'], 'string', 'max' => 255],
        ];
    }

    public static function statusLabels()
    {
        return [
            self::STATUS_ON_TREE => self::STATUS_ON_TREE_LABEL,
            self::STATUS_DROPPED => self::STATUS_DROPPED_LABEL,
            self::STATUS_ROTTEN => self::STATUS_ROTTEN_LABEL,
        ];
    }

    /**
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::beforeSave()
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            // цвет устанавливается при создании объекта случайным, случайная дата появления
            $this->color = self::getRandomAppleColor();
            $this->createdAt = rand(time(), time() + \Yii::$app->params['timePeriod']);
        } else {
            if ($this->volume <= 0) {
                $this->delete();
            }
        }

        // логируем дату изменения
        $this->updatedAt = time();
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        // при падении
        if (!$insert && !empty($changedAttributes['status']) && $changedAttributes['status'] == self::STATUS_ON_TREE && $this->status == self::STATUS_DROPPED) {
            // логируем время падения
            $this->droppedAt = time();
            $this->save();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'appleId' => 'ID яблока',
            'color' => 'Цвет',
            'volume' => 'Оставшийся объем',
            'status' => 'Статус',
            'droppedAt' => 'Когда упало',
            'createdAt' => 'Когда появилось',
            'updatedAt' => 'Когда было изменено',
        ];
    }

    /**
     * Получение рандомного цвета из массива params
     * @return mixed
     */
    private function getRandomAppleColor()
    {
        $array = \Yii::$app->params['appleColors'];
        $randomIndex = rand(1, count($array));

        return $array[$randomIndex];
    }

    /**
     * Можно ли откусить
     * @return bool
     */
    public function isPossibleEat()
    {
        if ($this->status == self::STATUS_ON_TREE || $this->status == self::STATUS_ROTTEN) {
            return false;
        }

        if ($this->status == self::STATUS_DROPPED && $this->droppedAt + \Yii::$app->params['rottenPeriod'] < time()) {
            $this->status = self::STATUS_ROTTEN;
            $this->save();
            return false;
        }

        return true;
    }

    /**
     * Откусить
     * @param $percent
     */
    public function eat($percent)
    {
        $eatPartDecimal = $percent / 100;
        $this->volume -= $eatPartDecimal;

        $this->save();
    }

    public function drop(Apple $apple)
    {
        $apple->status = Apple::STATUS_DROPPED;
        $apple->droppedAt = time();
        $apple->save();
    }
}
