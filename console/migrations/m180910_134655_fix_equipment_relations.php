<?php

/**
 * Class m180910_134655_fix_equipment_realtions
 */
class m180910_134655_fix_equipment_relations extends \console\yii2\Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->dropForeignKey('fk-equipment-houseUuid', '{{%equipment}}');
        $this->dropColumn('{{%equipment}}', 'houseUuid');
        $this->addColumn('{{%equipment}}', 'houseUuid', $this->string()->null());
        $this->addForeignKey(
            'fk_equipment_houseUuid__house_uuid',
            'equipment',
            'houseUuid',
            'house',
            'uuid',
            self::FK_RESTRICT,
            self::FK_CASCADE
        );

        $this->dropForeignKey('fk-equipment-flatUuid', '{{%equipment}}');
        $this->dropColumn('{{%equipment}}', 'flatUuid');
        $this->addColumn('{{%equipment}}', 'flatUuid', $this->string()->null());
        $this->addForeignKey(
            'fk_equipment_flatUuid__flat_uuid',
            'equipment',
            'flatUuid',
            'flat',
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
        echo "m180910_134655_fix_equipment_realtions cannot be reverted.\n";

        return false;
    }
}
