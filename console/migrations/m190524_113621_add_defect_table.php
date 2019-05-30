<?php

use yii\db\Migration;

/**
 * Class m190524_113621_add_defect_table
 */
class m190524_113621_add_defect_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%defect}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'title' => $this->string(500)->notNull(),
            'userUuid' => $this->string(45)->notNull(),
            'taskUuid' => $this->string(45),
            'equipmentUuid' => $this->string(45)->notNull(),
            'date' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'defectStatus' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-userUuid',
            'defect',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-defect-userUuid',
            'defect',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-equipmentUuid',
            'defect',
            'equipmentUuid'
        );

        $this->addForeignKey(
            'fk-defect-equipmentUuid',
            'defect',
            'equipmentUuid',
            'equipment',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-taskUuid',
            'defect',
            'taskUuid'
        );

        $this->addForeignKey(
            'fk-defect-taskUuid',
            'defect',
            'taskUuid',
            'task',
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
        $this->dropTable('defect');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190524_113621_add_defect_table cannot be reverted.\n";

        return false;
    }
    */
}
