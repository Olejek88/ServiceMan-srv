<?php

use common\models\Settings;
use yii\db\Migration;

/**
 * Class m190906_040741_add_settings
 */
class m190906_040741_add_settings extends Migration
{
    const SETTINGS = '{{%settings}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::SETTINGS, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'parameter' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $currentTime = date('Y-m-d\TH:i:s');
        $this->insert('settings', [
            'uuid' => Settings::SETTING_TASK_PAUSE_BEFORE_WARNING,
            'title' => 'Время на получение задачи до выдачи предупреждения',
            'parameter' => '2',
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);

        $this->insert('settings', [
            'uuid' => Settings::SETTING_SHOW_WARNINGS,
            'title' => 'Показывать предупреждения в таблице задач',
            'parameter' => '1',
            'createdAt' => $currentTime,
            'changedAt' => $currentTime
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190906_040741_add_settings cannot be reverted.\n";

        return false;
    }
}
