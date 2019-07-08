<?php

use yii\db\Migration;

/**
 * Class m190705_103907_add_coordinates
 */
class m190705_103907_add_coordinates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('house', "latitude", $this->double()->notNull()->defaultValue(55.14));
        $this->addColumn('house', "longitude", $this->double()->notNull()->defaultValue(61.43));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190705_103907_add_coordinates cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190705_103907_add_coordinates cannot be reverted.\n";

        return false;
    }
    */
}
