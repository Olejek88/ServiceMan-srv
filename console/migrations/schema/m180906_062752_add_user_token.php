<?php

/**
 * Class m180906_062752_add_user_token
 */
class m180906_062752_add_user_token extends \console\yii2\Migration
{
    const USER_TOKEN = '{{%user_token}}';
    const USER = '{{%user}}';
    const FK_USER_TOKEN2USER = 'fk_user_token_user_id__user_id';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::USER_TOKEN, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string(32)->notNull(),
            'valid_till' => $this->dateTime()->notNull(),
            'status' => $this->smallInteger(),
            'last_access' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey(
            self::FK_USER_TOKEN2USER,
            self::USER_TOKEN,
            'user_id',
            self::USER,
            '_id',
            self::FK_CASCADE,
            self::FK_CASCADE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m180906_062752_add_user_token cannot be reverted.\n";

        $this->dropForeignKey(self::FK_USER_TOKEN2USER, self::USER_TOKEN);

        $this->dropTable(self::USER_TOKEN);

        return true;
    }
}
