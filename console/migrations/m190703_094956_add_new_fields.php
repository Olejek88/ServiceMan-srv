<?php

use common\models\DefectType;
use yii\db\Migration;

/**
 * Class m190703_094956_add_new_fields
 */
class m190703_094956_add_new_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        $this->addColumn('receipt','userCheckWho', $this->string()->defaultValue('не указана'));
        $this->addColumn('contragent','account', $this->string()->defaultValue('не указан'));
        $this->createTable('defect_type', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->insertIntoType('defect_type', DefectType::DEFECT_DEFAULT,'Не отпределен');
        $this->addColumn('defect','defectTypeUuid', $this->string()->notNull()->defaultValue(DefectType::DEFECT_DEFAULT));

        $this->createIndex(
            'idx-defectTypeUuid',
            'defect',
            'defectTypeUuid'
        );

        $this->addForeignKey(
            'fk-defect-defectTypeUuid',
            'defect',
            'defectTypeUuid',
            'defect_type',
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
        echo "m190703_094956_add_new_fields cannot be reverted.\n";

        return false;
    }

    private function insertIntoType($table, $uuid, $title) {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }

}
