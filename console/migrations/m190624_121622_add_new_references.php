<?php

use common\models\HouseType;
use yii\db\Migration;

/**
 * Class m190624_121622_add_new_references
 */
class m190624_121622_add_new_references extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insertIntoType('house_type', HouseType::HOUSE_TYPE_MKD,
            'Многокваритирный дом', $currentTime, $currentTime);
        $this->insertIntoType('house_type', HouseType::HOUSE_TYPE_COMMERCE,
            'Коммерческий объект', $currentTime, $currentTime);
        $this->insertIntoType('house_type', HouseType::HOUSE_TYPE_BUDGET,
            'Бюджетное учереждение', $currentTime, $currentTime);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190624_121622_add_new_references cannot be reverted.\n";

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
