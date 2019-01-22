<?php

use yii\db\Migration;

/**
 * Class m190121_112918_add_equipment_register
 */
class m190121_112918_add_equipment_register extends Migration
{
    const EQUIPMENT_REGISTER_TYPE = '{{%equipment_register_type}}';
    const EQUIPMENT_REGISTER = '{{%equipment_register}}';

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
        $this->createTable(self::EQUIPMENT_REGISTER_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull(),
        ],$tableOptions);

        $this->createTable(self::EQUIPMENT_REGISTER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'equipmentUuid' => $this->string()->notNull(),
            'registerTypeUuid' => $this->string()->notNull(),
            'userUuid' => $this->string()->notNull(),
            'fromParameterUuid' => $this->string()->notNull(),
            'toParameterUuid' => $this->string()->notNull(),
            'date' => $this->dateTime()->notNull(),
            'createdAt' => $this->dateTime()->notNull(),
            'changedAt' => $this->dateTime()->notNull(),
        ],$tableOptions);

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
