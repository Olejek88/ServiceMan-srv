<?php

use yii\db\Migration;

/**
 * Class m191206_124941_add_extId_integration
 */
class m191206_124941_add_extId_integration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // поле для сохранения id пользователя во внешней системе с которой интегрируемся
        $this->addColumn('{{%contragent}}', 'extId', $this->string(64)->null());
        $this->createIndex('contragent-extId-idx', '{{%contragent}}', 'extId');
        // поле для сохранения id обращения во внешней системе с которой интегрируемся
        $this->addColumn('{{%request}}', 'extId', $this->string(64)->null());
        $this->createIndex('request-extId-idx', '{{%request}}', 'extId');
        // поле для сохранения класса контроллера внешней системы с которой интегрируемся, для выполнения
        // специфических действий для каждой системы)
        $this->addColumn('{{%request}}', 'integrationClass', $this->string(128)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191206_124941_add_extId_integration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191206_124941_add_extId_integration cannot be reverted.\n";

        return false;
    }
    */
}
