<?php

use yii\db\Migration;

/**
 * Class m180926_091958_add_user_street
 */
class m180926_091958_add_user_street extends Migration
{
    const USER_STREET = '{{%user_street}}';
    const FK_RESTRICT = 'RESTRICT';
    const FK_CASCADE = 'CASCADE';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::USER_STREET, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'userUuid' => $this->string()->notNull(),
            'streetUuid' => $this->string()->notNull(),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_user_street_userUuid__user_uuid',
            self::USER_STREET,
            'userUuid',
            'users',
            'uuid',
            self::FK_RESTRICT,
            self::FK_CASCADE
        );

        $this->addForeignKey(
            'fk_user_street_streetUuid__house_uuid',
            self::USER_STREET,
            'streetUuid',
            'street',
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
        echo "m180926_091958_add_user_street cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180926_091958_add_user_street cannot be reverted.\n";

        return false;
    }
    */
}