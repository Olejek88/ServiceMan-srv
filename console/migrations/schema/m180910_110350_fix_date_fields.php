<?php

use yii\db\Migration;

/**
 * Class m180910_110350_fix_date_fields
 */
class m180910_110350_fix_date_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $type = $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP');
        $tables = [
            '{{%city}}',
            '{{%street}}',
            '{{%house_status}}',
            '{{%flat_status}}',
            '{{%house}}',
            '{{%flat}}',
            '{{%resident}}',
            '{{%subject}}',
            '{{%alarm_type}}',
            '{{%alarm_status}}',
            '{{%users}}',
            '{{%alarm}}',
            '{{%equipment_type}}',
            '{{%equipment_status}}',
            '{{%equipment}}',
            '{{%control_point_type}}',
            '{{%photo_house}}',
            '{{%photo_flat}}',
            '{{%photo_equipment}}',
            '{{%photo_alarm}}',
            '{{%measure}}',
            '{{%gps_track}}',
        ];
        foreach ($tables as $table) {
            $this->dropColumn($table, 'createdAt');
            $this->addColumn($table, 'createdAt', $type);
        }

        $type = $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00');
        $table = '{{%subject}}';
        $this->dropColumn($table, 'contractDate');
        $this->addColumn($table, 'contractDate', $type);
        $table = '{{%equipment}}';
        $this->dropColumn($table, 'testDate');
        $this->addColumn($table, 'testDate', $type);
        $table = '{{%alarm}}';
        $this->dropColumn($table, 'date');
        $this->addColumn($table, 'date', $type);
        $table = '{{%measure}}';
        $this->dropColumn($table, 'date');
        $this->addColumn($table, 'date', $type);
        $table = '{{%gps_track}}';
        $this->dropColumn($table, 'date');
        $this->addColumn($table, 'date', $type);
        $table = '{{%journal}}';
        $this->dropColumn($table, 'date');
        $this->addColumn($table, 'date', $type);

    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m180910_110350_fix_date_fields cannot be reverted.\n";

        return false;
    }
}
