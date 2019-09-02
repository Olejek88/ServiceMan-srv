<?php

use yii\db\Migration;
use common\models\HouseType;

/**
 * Class m190902_121756_fix_house_type
 */
class m190902_121756_fix_house_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->dropForeignKey('fk-house_type-organization-oid', '{{%house_type}}');
        $this->dropColumn('{{%house_type}}', 'oid');
        $types = [
            HouseType::HOUSE_TYPE_PRIVATE => 'Частный дом',
            HouseType::HOUSE_TYPE_TOWNHOUSE => 'Таунхаус',
            HouseType::HOUSE_TYPE_BUDGET => 'Бюджетное учереждение',
            HouseType::HOUSE_TYPE_COMMERCE => 'Коммерческий объект',
            HouseType::HOUSE_TYPE_MKD => 'Многоквартирный дом',
        ];

        foreach ($types as $uuid => $title) {
            $row = $this->db->createCommand("select * from house_type where uuid='" . $uuid . "'")->query();
            if ($row->count() == 0) {
                $this->insert('{{%house_type}}', [
                    'uuid' => $uuid,
                    'title' => $title,
                    'createdAt' => $currentTime,
                    'changedAt' => $currentTime
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190902_121756_fix_house_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190902_121756_fix_house_type cannot be reverted.\n";

        return false;
    }
    */
}
