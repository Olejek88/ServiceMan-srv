<?php

use common\models\Contragent;
use common\models\ContragentType;
use common\models\Organization;
use yii\db\Migration;

/**
 * Class m190707_103621_insert_new_references
 */
class m190707_103621_insert_new_references extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insertIntoType('contragent_type', ContragentType::CONTRACTOR, 'Подрядная организация');
        $this->insertIntoType('contragent_type', ContragentType::ORGANIZATION, 'Коммерческая организация');

        $currentTime = date('Y-m-d\TH:i:s');
        $this->insert('contragent', [
            'uuid' => Contragent::DEFAULT_CONTRAGENT,
            'oid' => Organization::ORG_SERVICE_UUID,
            'title' => 'Контрагент по-умолчанию',
            'address' => '-',
            'phone' => '-',
            'inn' => '-',
            'account' => 'не указан',
            'director' => '-',
            'email' => '-',
            'contragentTypeUuid' => ContragentType::WORKER,
            'deleted' => 0,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190707_103621_insert_new_references cannot be reverted.\n";

        return false;
    }

    private function insertIntoType($table, $uuid, $title)
    {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }
}
