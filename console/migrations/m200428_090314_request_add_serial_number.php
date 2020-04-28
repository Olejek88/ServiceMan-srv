<?php

use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m200428_090314_request_add_serial_number
 */
class m200428_090314_request_add_serial_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%request}}', 'serialNumber', $this->integer()->defaultValue(0)->notNull());
        $this->createIndex('idx-request-serial-number', '{{%request}}', 'serialNumber');

        // проставляем порядковый номер согласно текущему алгоритму
        $qs = 'select oid, date_format(createdAt, "%Y-01-01") y, min(_id) _id from (select * from request order by _id)
            r group by oid, y';
        $firstIdByOrgYears = $this->db->createCommand($qs)->queryAll();
        foreach ($firstIdByOrgYears as $idByOrgYear) {
            $qs = 'update request set serialNumber = (_id - :minId + 1) where oid=:oid and createdAt>=:start and createdAt < :end';
            $rc = $this->db->createCommand($qs, [
                ':oid' => $idByOrgYear['oid'],
                ':minId' => $idByOrgYear['_id'],
                ':start' => $idByOrgYear['y'],
                ':end' => date('Y-01-01', strtotime('+1 year', strtotime($idByOrgYear['y']))),
            ])->execute();
            echo Console::ansiFormat("oid:" . $idByOrgYear['oid'] . " rc=$rc", [Console::FG_GREEN]) . PHP_EOL;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200428_090314_request_add_serial_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200428_090314_request_add_serial_number cannot be reverted.\n";

        return false;
    }
    */
}
