<?php

use yii\db\Migration;

/**
 * Class m190121_112918_add_equipment_register
 */
class m190121_112918_add_equipment_register extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $createdAt = 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP';
        $changedAt = 'TIMESTAMP NOT NULL DEFAULT "0000-00-00 00:00:00"';

        $this->createTable('{{%equipment_register_type}}', [
            '_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            0 => 'PRIMARY KEY (`_id`)',
            'uuid' => 'VARCHAR(50) NOT NULL',
            'title' => 'VARCHAR(100) NOT NULL',
            'createdAt' => $createdAt,
            'changedAt' => $changedAt,
        ], $tableOptions);

        $this->createTable('{{%equipment_register}}', [
            '_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            0 => 'PRIMARY KEY (`_id`)',
            'uuid' => 'VARCHAR(50) NOT NULL',
            'equipmentUuid' => 'VARCHAR(45) NOT NULL',
            'registerTypeUuid' => 'VARCHAR(45) NOT NULL',
            'userUuid' => 'VARCHAR(45) NOT NULL',
            'date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ',
            'fromParameterUuid' => 'VARCHAR(45) NOT NULL',
            'toParameterUuid' => 'VARCHAR(45) NOT NULL',
        ], $tableOptions);

        $this->createIndex(
            'idx-equipment_register_type-uuid',
            'equipment_register_type',
            'uuid'
        );

        $this->createIndex(
            'idx-equipment_register-equipmentUuid',
            'equipment_register',
            'equipmentUuid'
        );
        $this->createIndex(
            'idx-equipment_register-registerTypeUuid',
            'equipment_register',
            'registerTypeUuid'
        );
        $this->createIndex(
            'idx-equipment_register-userUuid',
            'equipment_register',
            'userUuid'
        );
        $this->createIndex(
            'idx-equipment_register-fromParameterUuid',
            'equipment_register',
            'fromParameterUuid'
        );
        $this->createIndex(
            'idx-equipment_register-toParameterUuid',
            'equipment_register',
            'toParameterUuid'
        );

        $this->addForeignKey(
            'fk-equipment_register-userUuid',
            'equipment_register',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
        $this->addForeignKey(
            'fk-equipment_register-equipmentUuid',
            'equipment_register',
            'equipmentUuid',
            'equipment',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
        $this->addForeignKey(
            'fk-equipment_register-registerTypeUuid',
            'equipment_register',
            'registerTypeUuid',
            'equipment_register_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $currentTime = date('Y-m-d\TH:i:s');
        $this->insertIntoRegisterType(1, '2D3AD301-FD41-4A45-A18B-6CD13526CFDD', 'Смена статуса', $currentTime, $currentTime);
        $this->insertIntoRegisterType(2, 'BE1D4149-2563-4771-88DC-2EB8B3DA684F', 'Смена местоположения', $currentTime, $currentTime);
        $this->insertIntoRegisterType(3, '4C74019F-45A9-43Ab-9B97-4D077F8BF3FA', 'Изменение свойств', $currentTime, $currentTime);
        $this->insertIntoRegisterType(4, '0063E563-9277-4D06-897D-65261BB2D4AB', 'Вердикт по оборудованию', $currentTime, $currentTime);
        $this->insertIntoRegisterType(5, '885E8388-8205-43CA-B55E-3B61D01019AD', 'Изменение модели', $currentTime, $currentTime);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190121_112918_add_equipment_register cannot be reverted.\n";

        $this->dropIndex(
            'idx-equipment_register-equipmentUuid',
            'equipment_register'
        );
        $this->dropIndex(
            'idx-equipment_register-registerTypeUuid',
            'equipment_register'
        );
        $this->dropIndex(
            'idx-equipment_register-userUuid',
            'equipment_register'
        );
        $this->dropIndex(
            'idx-equipment_register-fromParameterUuid',
            'equipment_register'
        );
        $this->dropIndex(
            'idx-equipment_register-toParameterUuid',
            'equipment_register'
        );

        $this->dropForeignKey(
            'fk-equipment_register-userUuid',
            'equipment_register'
        );
        $this->dropForeignKey(
            'fk-equipment_register-equipmentUuid',
            'equipment_register'
        );
        $this->dropForeignKey(
            'fk-equipment_register-registerTypeUuid',
            'equipment_register'
        );
        $this->dropForeignKey(
            'fk-equipment_register-fromParameterUuid',
            'equipment_register'
        );
        $this->dropForeignKey(
            'fk-equipment_register-toParameterUuid',
            'equipment_register'
        );

        $this->dropTable('equipment_register');

        return false;
    }

    private function insertIntoRegisterType($id, $uuid, $title, $createdAt, $changedAt)
    {
        $this->insert('equipment_register_type', [
            '_id' => $id,
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190121_112918_add_equipment_register cannot be reverted.\n";

        return false;
    }
    */
}
