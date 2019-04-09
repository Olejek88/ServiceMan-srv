<?php

/**
 * Class m180924_131947_add_owner_field_delete_title_from_house
 */
class m180924_131947_add_owner_field_subject extends \console\yii2\Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%subject}}', 'owner', $this->string()->defaultValue("Организация")->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180924_131947_add_owner_field_delete_title_from_house cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180924_131947_add_owner_field_delete_title_from_house cannot be reverted.\n";

        return false;
    }
    */
}
