<?php

use \common\models\HouseType;
use \common\models\FlatType;

/**
 * Class m180918_113943_add_ref_data
 */
class m180918_113943_add_ref_data extends \console\yii2\Migration
{
    const FLAT = '{{%flat}}';
    const FLAT_TYPE = '{{%flat_type}}';
    const HOUSE = '{{%house}}';
    const HOUSE_TYPE = '{{%house_type}}';
    const FK_FLAT2FLAT_TYPE = 'fk_flat_flatTypeUuid__flat_type_uuid';
    const FK_HOUSE2HOUSE_TYPE = 'fk_house_houseTypeUuid__house_type_uuid';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $date = date('Y-m-d\TH:i:s');
        $this->insert(self::FLAT_TYPE, [
            'uuid' => FlatType::FLAT_TYPE_GENERAL,
            'title' => 'Основная',
            'createdAt' => $date,
            'changedAt' => $date,
        ]);
        $this->insert(self::FLAT_TYPE, [
            'uuid' => FlatType::FLAT_TYPE_COMMERCE,
            'title' => 'Комерческая',
            'createdAt' => $date,
            'changedAt' => $date,
        ]);
        $this->insert(self::FLAT_TYPE, [
            'uuid' => FlatType::FLAT_TYPE_INPUT,
            'title' => 'Вводная',
            'createdAt' => $date,
            'changedAt' => $date,
        ]);

        $this->insert(self::HOUSE_TYPE, [
            'uuid' => HouseType::HOUSE_TYPE_PRIVATE,
            'title' => 'Частный',
            'createdAt' => $date,
            'changedAt' => $date,
        ]);

        $this->addForeignKey(
            self::FK_FLAT2FLAT_TYPE,
            self::FLAT,
            'flatTypeUuid',
            self::FLAT_TYPE,
            'uuid',
            self::FK_RESTRICT,
            self::FK_CASCADE
        );

        $this->addForeignKey(
            self::FK_HOUSE2HOUSE_TYPE,
            self::HOUSE,
            'houseTypeUuid',
            self::HOUSE_TYPE,
            'uuid',
            self::FK_RESTRICT,
            self::FK_CASCADE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m180918_113943_add_ref_data cannot be reverted.\n";

        return false;
    }
}
