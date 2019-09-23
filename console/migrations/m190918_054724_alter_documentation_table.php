<?php

use yii\db\Migration;

/**
 * Class m190918_054724_alter_documentation_table
 */
class m190918_054724_alter_documentation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%documentation}}', 'houseUuid', $this->string(45));
        $this->createIndex(
            'idx-objectUuid',
            '{{%documentation}}',
            'houseUuid'
        );

        $this->addForeignKey(
            'fk-documentation-houseUuid',
            '{{%documentation}}',
            'houseUuid',
            'house',
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
        echo "m190918_054724_alter_documentation_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190918_054724_alter_documentation_table cannot be reverted.\n";

        return false;
    }
    */
}
