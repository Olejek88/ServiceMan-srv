<?php

use common\models\ContragentType;
use yii\db\Migration;

/**
 * Class m190723_114001_insert_new_references2
 */
class m190723_114001_insert_new_references2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insertIntoType('contragent_type', ContragentType::CITIZEN, 'Физическое лицо');
        $this->dropIndex('inn', 'contragent');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190723_114001_insert_new_references2 cannot be reverted.\n";

        return true;
    }

    private function insertIntoType($table, $uuid, $title)
    {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190723_114001_insert_new_references2 cannot be reverted.\n";

        return false;
    }
    */
}
