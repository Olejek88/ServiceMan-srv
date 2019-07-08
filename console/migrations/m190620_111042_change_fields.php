<?php

use yii\db\Migration;

/**
 * Class m190620_111042_cheange_fields
 */
class m190620_111042_change_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('request','userCheck');
        $this->addColumn('receipt','userCheck', $this->string()->defaultValue('не назначен'));


        $this->addColumn('task','authorUuid', $this->string(45));
        $this->createIndex(
            'idx-authorUuid',
            'task',
            'authorUuid'
        );

        $this->addForeignKey(
            'fk-task-authorUuid',
            'task',
            'authorUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190620_111042_cheange_fields cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190620_111042_cheange_fields cannot be reverted.\n";

        return false;
    }
    */
}
