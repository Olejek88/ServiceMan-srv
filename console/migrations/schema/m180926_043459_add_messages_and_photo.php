<?php

use yii\db\Migration;

/**
 * Class m180926_043459_add_messages_and_photo
 */
class m180926_043459_add_messages_and_photo extends Migration
{
    const MESSAGE = '{{%message}}';
    const PHOTO_MESSAGE = '{{%photo_message}}';
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

        $this->createTable(self::MESSAGE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'userUuid' => $this->string()->notNull(),
            'flatUuid' => $this->string()->notNull(),
            'message' => $this->string()->notNull()->defaultValue(''),
            'date' => $this->dateTime()->notNull(),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_message_userUuid__user_uuid',
            self::MESSAGE,
            'userUuid',
            'users',
            'uuid',
            self::FK_RESTRICT,
            self::FK_CASCADE
        );

        $this->addForeignKey(
            'fk_message_flatUuid__flat_uuid',
            self::MESSAGE,
            'flatUuid',
            'flat',
            'uuid',
            self::FK_RESTRICT,
            self::FK_CASCADE
        );

        $this->createTable(self::PHOTO_MESSAGE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'messageUuid' => $this->string()->notNull(),
            'longitude' => $this->double()->defaultValue('56.0366'),
            'latitude' => $this->double()->defaultValue('59.5536'),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull()
        ], $tableOptions);

        $this->addForeignKey(
            'fk_photo_message_messageUuid__message_uuid',
            self::PHOTO_MESSAGE,
            'messageUuid',
            'message',
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
        echo "m180926_043459_add_messages_and_photo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180926_043459_add_messages_and_photo cannot be reverted.\n";

        return false;
    }
    */
}
