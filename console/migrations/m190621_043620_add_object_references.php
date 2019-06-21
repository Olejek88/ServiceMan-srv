<?php

use common\models\TaskType;
use yii\db\Migration;

/**
 * Class m190621_043620_add_object_references
 */
class m190621_043620_add_object_references extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');

        $this->insertIntoType('object_type','80237148-9DBB-4315-A99D-D83CA5258C69',
            'Квартира', $currentTime, $currentTime);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190621_043620_add_object_references cannot be reverted.\n";

        return false;
    }

    private function insertIntoType($table, $uuid, $title, $createdAt, $changedAt) {
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }
}
