<?php

use \common\models\ObjectType;
use common\models\HouseType;

/**
 * Class m180918_111640_add_lost_tables
 */
class m180918_111640_add_lost_tables extends \console\yii2\Migration
{
    const FLAT = '{{%flat}}';
    const HOUSE = '{{%house}}';
    const FLAT_TYPE = '{{%flat_type}}';
    const HOUSE_TYPE = '{{%house_type}}';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::FLAT_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull()->defaultValue(''),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull(),
        ]);

        $this->createTable(self::HOUSE_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull()->defaultValue(''),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull(),
        ]);

        $this->addColumn(self::FLAT, 'flatTypeUuid', $this->string()->notNull()
            ->defaultValue(\common\models\ObjectType::FLAT_TYPE_GENERAL));
        $this->addColumn(self::FLAT, 'title', $this->string()->notNull()->defaultValue(''));

        $this->addColumn(self::HOUSE, 'houseTypeUuid', $this->string()->notNull()
            ->defaultValue(\common\models\HouseType::HOUSE_TYPE_PRIVATE));
        $this->addColumn(self::HOUSE, 'title', $this->string()->notNull()->defaultValue(''));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m180918_111640_add_lost_tables cannot be reverted.\n";

        return false;
    }

}
