<?php

use yii\db\Migration;

/**
 * Class m190730_134359_add_measure_type
 */
class m190730_134359_add_measure_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $date = date('Y-m-d H:i:s');
        $this->insert('{{%measure_type}}', [
            'uuid' => 'E9ADE49A-3C31-42F8-A751-AAEB890C2190',
            'title' => 'Безразмерная',
            'createdAt' => $date,
            'changedAt' => $date,
        ]);
        $this->insert('{{%measure_type}}', [
            'uuid' => '481C2E40-421E-41AB-8BC1-5FB0D01A4CC3',
            'title' => 'Частота',
            'createdAt' => $date,
            'changedAt' => $date,
        ]);
        $this->insert('{{%measure_type}}', [
            'uuid' => '1BEC4685-466F-4AA6-95FC-A3C01BAF09FE',
            'title' => 'Напряжение',
            'createdAt' => $date,
            'changedAt' => $date,
        ]);
        $this->insert('{{%measure_type}}', [
            'uuid' => '69A71072-7EDD-4FF9-B095-0EF145286D79',
            'title' => 'Давление',
            'createdAt' => $date,
            'changedAt' => $date,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190730_134359_add_measure_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190730_134359_add_measure_type cannot be reverted.\n";

        return false;
    }
    */
}
