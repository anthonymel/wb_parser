<?php

use yii\db\Migration;

/**
 * Class m200420_083722_AppleTable
 */
class m200420_083722_AppleTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('apple', [
            'appleId' => $this->primaryKey(),
            'color' => $this->string()->notNull(),
            'volume' => $this->decimal(3, 2)->defaultValue(1.00),
            'status' => $this->integer()->defaultValue(0),
            'droppedAt' => $this->bigInteger(),
            'createdAt' => $this->bigInteger(),
            'updatedAt' => $this->bigInteger()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('apple');
    }
}
