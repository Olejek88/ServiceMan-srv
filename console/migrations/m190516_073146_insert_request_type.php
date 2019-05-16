<?php

use yii\db\Migration;

/**
 * Class m190516_073146_insert_request_type
 */
class m190516_073146_insert_request_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%request}}', 'type', $this->integer()->defaultValue(0));
        $this->addColumn('{{%request}}', 'result', $this->string(512)->defaultValue("заявка выполнена"));
        $this->addColumn('{{%request}}', 'verdict', $this->string(512)->defaultValue("нет задачи"));

        $this->createIndex(
            'idx-userUuid',
            'request',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-request-userUuid',
            'request',
            'userUuid',
            'contragent',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-authorUuid',
            'request',
            'authorUuid'
        );

        $this->addForeignKey(
            'fk-request-authorUuid',
            'request',
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
        echo "m190516_073146_insert_request_type cannot be reverted.\n";

        return true;
    }
}
