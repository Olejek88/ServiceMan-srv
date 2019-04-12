<?php

/**
 * Class m180924_103102_change_flat_house_tables
 */
class m180924_103102_change_flat_house_tables extends \console\yii2\Migration
{
    const FK_RESTRICT = 'RESTRICT';
    const FK_CASCADE = 'CASCADE';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%flat}}', 'title');
        $this->addColumn('{{%subject}}', 'flatUuid', $this->string()->null());
        $this->addForeignKey(
            'fk_subject_flatUuid__flat_uuid',
            'subject',
            'flatUuid',
            'flat',
            'uuid',
            self::FK_RESTRICT,
            self::FK_CASCADE
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180924_103102_change_flat_house_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180924_103102_change_flat_house_tables cannot be reverted.\n";

        return false;
    }
    */
}
