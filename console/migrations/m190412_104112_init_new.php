<?php

use console\yii2\Migration;

/**
 * Class m190412_104112_init_new
 */
class m190412_104112_init_new extends Migration
{
    /**
     * {@inheritdoc}
     */
    const EQUIPMENT_REGISTER_TYPE = '{{%equipment_register_type}}';
    const EQUIPMENT_REGISTER = '{{%equipment_register}}';
    const HOUSE = '{{%house}}';
    const JOURNAL = '{{%journal}}';
    const FLAT = '{{%flat}}';
    const FLAT_TYPE = '{{%flat_type}}';
    const HOUSE_TYPE = '{{%house_type}}';
    const MESSAGE = '{{%message}}';
    const PHOTO_MESSAGE = '{{%photo_message}}';
    const OPERATION = '{{%operation}}';
    const OPERATION_TEMPLATE = '{{%operation_template}}';
    const TASK = '{{%task}}';
    const USER_TOKEN = '{{%user_token}}';
    const USER = '{{%user}}';
    const USERS = '{{%users}}';
    const USER_HOUSE = '{{%user_house}}';
    const WORK_STATUS = '{{%work_status}}';

    const FK_FLAT2FLAT_TYPE = 'fk_flat_flatTypeUuid__flat_type_uuid';
    const FK_HOUSE2HOUSE_TYPE = 'fk_house_houseTypeUuid__house_type_uuid';
    const FK_USER_TOKEN2USER = 'fk_user_token_user_id__user_id';
    const FK_USERS2USER = 'fk_users_user_id__user_id';
    const FK_JOURNAL2USERS = 'fk_journal_useruuid__users_uuid';

    const FK_RESTRICT = 'RESTRICT';
    const FK_CASCADE = 'CASCADE';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            '_id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%users}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'user_id' => $this->integer()->notNull()->unique(),
            'type' => $this->integer()->notNull(),
            'active' => $this->integer()->notNull()->defaultValue(0),
            'name' => $this->string()->notNull(),
            'pin' => $this->string()->notNull(),
            'whoIs' => $this->string(45)->defaultValue(""),
            'image' => $this->string(),
            'contact' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-users-uuid',
            'users',
            'uuid'
        );

        $this->createTable('{{%city}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'gis_id' => $this->string(45),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%street}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45),
            'gis_id' => $this->string(45),
            'title' => $this->string()->notNull(),
            'cityUuid' => $this->string(45)->notNull(),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-cityUuid',
            'street',
            'cityUuid'
        );

        $this->addForeignKey(
            'fk-street-cityUuid',
            'street',
            'cityUuid',
            'city',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%house_status}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%house_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'gis_id' => $this->string(45),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%house}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'gis_id' => $this->string(45),
            'number' => $this->string()->notNull(),
            'houseStatusUuid' => $this->string(45)->notNull(),
            'houseTypeUuid' => $this->string(45)->notNull(),
            'streetUuid' => $this->string(45)->notNull(),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-streetUuid',
            'house',
            'streetUuid'
        );

        $this->addForeignKey(
            'fk-house-streetUuid',
            'house',
            'streetUuid',
            'street',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-houseStatusUuid',
            'house',
            'houseStatusUuid'
        );

        $this->addForeignKey(
            'fk-house-houseStatusUuid',
            'house',
            'houseStatusUuid',
            'house_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-houseTypeUuid',
            'house',
            'houseTypeUuid'
        );

        $this->addForeignKey(
            'fk-house-houseTypeUuid',
            'house',
            'houseTypeUuid',
            'house_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%object_status}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%object_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'gis_id' => $this->string(45),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%object}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'gis_id' => $this->string(45),
            'title' => $this->string()->notNull(),
            'objectStatusUuid' => $this->string()->notNull(),
            'houseUuid' => $this->string(45)->notNull(),
            'objectTypeUuid' => $this->string(45)->notNull(),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-houseUuid',
            'object',
            'houseUuid'
        );

        $this->addForeignKey(
            'fk-object-houseUuid',
            'object',
            'houseUuid',
            'house',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-objectStatusUuid',
            'object',
            'objectStatusUuid'
        );

        $this->addForeignKey(
            'fk-object-objectStatusUuid',
            'object',
            'objectStatusUuid',
            'object_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-objectTypeUuid',
            'object',
            'objectTypeUuid'
        );

        $this->addForeignKey(
            'fk-object-objectTypeUuid',
            'object',
            'objectTypeUuid',
            'object_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%photo}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'objectUuid' => $this->string(45)->notNull(),
            'userUuid' => $this->string(45)->notNull(),
            'longitude' => $this->double()->defaultValue('55'),
            'latitude' => $this->double()->defaultValue('55'),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-userUuid',
            'photo',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-photo-userUuid',
            'photo',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        //--------------------------------------------------------------------------------------------------------------
        $this->createTable('{{%alarm_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%alarm_status}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(
            '{{%alarm}}',
            [
                '_id' => $this->primaryKey()->comment("Id"),
                'uuid' => $this->string(45)->unique()->notNull(),
                'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
                'oid' => $this->string(45)->notNull(),
                'alarmTypeUuid' => $this->string(45)->notNull(),
                'alarmStatusUuid' => $this->string(45)->notNull(),
                'objectUuid' => $this->string(45)->notNull(),
                'userUuid' => $this->string(45)->notNull(),
                'comment' => $this->string(512)->notNull(),
                'longitude' => $this->double()->defaultValue('55'),
                'latitude' => $this->double()->defaultValue('55'),
                'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            ], $tableOptions
        );

        $this->createIndex(
            'idx-alarmTypeUuid',
            'alarm',
            'alarmTypeUuid'
        );

        $this->addForeignKey(
            'fk-alarm-alarmTypeUuid',
            'alarm',
            'alarmTypeUuid',
            'alarm_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-alarmStatusUuid',
            'alarm',
            'alarmStatusUuid'
        );

        $this->addForeignKey(
            'fk-alarm-alarmStatusUuid',
            'alarm',
            'alarmStatusUuid',
            'alarm_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-alarm-userUuid',
            'alarm',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-alarm-userUuid',
            'alarm',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-alarm-objectUuid',
            'alarm',
            'objectUuid'
        );

        $this->addForeignKey(
            'fk-alarm-objectUuid',
            'alarm',
            'objectUuid',
            'object',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        //--------------------------------------------------------------------------------------------------------------
        $this->createTable('{{%contragent_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%contragent}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'gis_id' => $this->string(45),
            'title' => $this->string()->notNull(),
            'address' => $this->string()->defaultValue("не указан"),
            'phone' => $this->string()->defaultValue("не указан"),
            'inn' => $this->string()->notNull()->unique(),
            'director' => $this->string()->defaultValue("не указан"),
            'email' => $this->string()->defaultValue("не указан"),
            'contragentTypeUuid' => $this->string(45)->notNull(),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);


        $this->createIndex(
            'idx-contragentTypeUuid',
            'contragent',
            'contragentTypeUuid'
        );

        $this->addForeignKey(
            'fk-contragent-contragentTypeUuid',
            'contragent',
            'contragentTypeUuid',
            'contragent_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%contragent_register}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'contragentUuid' => $this->string(45)->notNull(),
            'date' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'description' => $this->string()->defaultValue(""),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-contragentUuid',
            'contragent_register',
            'contragentUuid'
        );

        $this->addForeignKey(
            'fk-contragentRegister-contragentUuid',
            'contragent_register',
            'contragentUuid',
            'contragent',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
        //--------------------------------------------------------------------------------------------------------------

        $this->createTable('{{%equipment_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'equipmentSystemUuid' => $this->string(45)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%equipment_status}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%equipment_system}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'titleUser' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%equipment}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'title' => $this->string(150)->notNull(),
            'objectUuid' => $this->string(45)->notNull(),
            'equipmentTypeUuid' => $this->string(45)->notNull(),
            'equipmentStatusUuid' => $this->string(45)->notNull(),
            'tag' => $this->string(),
            'serial' => $this->string(),
            'testDate' => $this->timestamp()->defaultValue('2019-01-01'),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-objectUuid',
            'equipment',
            'objectUuid'
        );

        $this->addForeignKey(
            'fk-equipment-objectUuid',
            'equipment',
            'objectUuid',
            'object',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-equipmentTypeUuid',
            'equipment',
            'equipmentTypeUuid'
        );

        $this->addForeignKey(
            'fk-equipment-equipmentTypeUuid',
            'equipment',
            'equipmentTypeUuid',
            'equipment_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-equipmentStatusUuid',
            'equipment',
            'equipmentStatusUuid'
        );

        $this->addForeignKey(
            'fk-equipment-equipmentStatusUuid',
            'equipment',
            'equipmentStatusUuid',
            'equipment_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-equipmentSystemUuid',
            'equipment_type',
            'equipmentSystemUuid'
        );

        $this->addForeignKey(
            'fk-equipment_type-equipmentSystemUuid',
            'equipment_type',
            'equipmentSystemUuid',
            'equipment_system',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%documentation_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull(),
            'changedAt' => $this->timestamp()->notNull()
        ], $tableOptions);

        $this->createTable('{{%documentation}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'equipmentUuid' => $this->string(45),
            'documentationTypeUuid' => $this->string(45)->notNull(),
            'equipmentTypeUuid' => $this->string(45),
            'path' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createIndex(
            'idx-equipmentUuid',
            'documentation',
            'equipmentUuid'
        );

        $this->addForeignKey(
            'fk-documentation-equipmentUuid',
            'documentation',
            'equipmentUuid',
            'equipment',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-equipmentTypeUuid',
            'documentation',
            'equipmentTypeUuid'
        );

        $this->addForeignKey(
            'fk-documentation-equipmentTypeUuid',
            'documentation',
            'equipmentTypeUuid',
            'equipment_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-documentationTypeUuid',
            'documentation',
            'documentationTypeUuid'
        );

        $this->addForeignKey(
            'fk-documentation-documentationTypeUuid',
            'documentation',
            'documentationTypeUuid',
            'documentation_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%equipment_register_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createTable('{{%equipment_register}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'equipmentUuid' => $this->string(45)->notNull(),
            'userUuid' => $this->string(45)->notNull(),
            'registerTypeUuid' => $this->string(45)->notNull(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'description' => $this->string()->defaultValue(""),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-equipmentUuid',
            'equipment_register',
            'equipmentUuid'
        );

        $this->addForeignKey(
            'fk-equipmentRegister-equipmentUuid',
            'equipment_register',
            'equipmentUuid',
            'equipment',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userUuid',
            'equipment_register',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-equipmentRegister-userUuid',
            'equipment_register',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-registerTypeUuid',
            'equipment_register',
            'registerTypeUuid'
        );

        $this->addForeignKey(
            'fk-equipmentRegister-registerTypeUuid',
            'equipment_register',
            'registerTypeUuid',
            'equipment_register_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
        //--------------------------------------------------------------------------------------------------------------
        $this->createTable('{{%export_link}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'table' => $this->string(45)->notNull(),
            'dbUuid' => $this->string(45)->notNull(),
            'externalId' => $this->string(45)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%gps_track}}', [
            'userUuid' => $this->string(45)->notNull(),
            'date' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'longitude' => $this->double(),
            'latitude' => $this->double(),
            'sent' => $this->boolean(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ],
            $tableOptions);

        $this->createIndex(
            'idx-userUuid',
            'gps_track',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-gps_track-userUuid',
            'gps_track',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%journal}}', [
            '_id' => $this->primaryKey(),
            'userUuid' => $this->string(45)->notNull(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'description' => $this->string()->defaultValue("")
        ]);

        $this->createIndex(
            'idx-userUuid',
            'journal',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-journal-userUuid',
            'journal',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        //--------------------------------------------------------------------------------------------------------------

        $this->createTable('{{%measure_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createTable('{{%measure}}', ['_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'measureTypeUuid' => $this->string(45)->notNull(),
            'equipmentUuid' => $this->string(45)->notNull(),
            'userUuid' => $this->string(45)->notNull(),
            'value' => $this->double(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-equipmentUuid',
            'measure',
            'equipmentUuid'
        );

        $this->addForeignKey(
            'fk-measure-equipmentUuid',
            'measure',
            'equipmentUuid',
            'equipment',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userUuid',
            'measure',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-measure-userUuid',
            'measure',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-measureTypeUuid',
            'measure',
            'measureTypeUuid'
        );

        $this->addForeignKey(
            'fk-measure-measureTypeUuid',
            'measure',
            'measureTypeUuid',
            'measure_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
        //--------------------------------------------------------------------------------------------------------------

        $this->createTable('{{%messages}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'fromUserUuid' => $this->string(45)->notNull(),
            'toUserUuid' => $this->string(45)->notNull(),
            'status' => $this->string()->notNull(),
            'text' => $this->string()->notNull(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-fromUserUuid',
            'messages',
            'fromUserUuid'
        );

        $this->addForeignKey(
            'fk-fromUser-fromUserUuid',
            'messages',
            'fromUserUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-toUserUuid',
            'messages',
            'toUserUuid'
        );

        $this->addForeignKey(
            'fk-toUser-toUserUuid',
            'messages',
            'toUserUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%object_contragent}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'objectUuid' => $this->string(45)->notNull(),
            'contragentUuid' => $this->string(45)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-objectUuid',
            'object_contragent',
            'objectUuid'
        );

        $this->addForeignKey(
            'fk-object_contragent-objectUuid',
            'object_contragent',
            'objectUuid',
            'object',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-contragentUuid',
            'object_contragent',
            'contragentUuid'
        );

        $this->addForeignKey(
            'fk-object_contragent-contragentUuid',
            'object_contragent',
            'contragentUuid',
            'contragent',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%operation_template}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'title' => $this->string()->notNull(),
            'description' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%request_status}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%request_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);


        $this->createTable('{{%request}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'userUuid' => $this->string(45),
            'contragentUuid' => $this->string(45),
            'authorUuid' => $this->string(45)->notNull(),
            'requestStatusUuid' => $this->string(45)->notNull(),
            'requestTypeUuid' => $this->string(45)->notNull(),
            'comment' => $this->string(),
            'equipmentUuid' => $this->string(45)->notNull(),
            'objectUuid' => $this->string(45),
            'taskUuid' => $this->string(45),
            'closeDate' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-requestStatusUuid',
            'request',
            'requestStatusUuid'
        );

        $this->addForeignKey(
            'fk-request-requestStatusUuid',
            'request',
            'requestStatusUuid',
            'request_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-requestTypeUuid',
            'request',
            'requestTypeUuid'
        );

        $this->addForeignKey(
            'fk-request-requestTypeUuid',
            'request',
            'requestTypeUuid',
            'request_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-equipmentUuid',
            'request',
            'equipmentUuid'
        );

        $this->addForeignKey(
            'fk-request-equipmentUuid',
            'request',
            'equipmentUuid',
            'equipment',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-objectUuid',
            'request',
            'objectUuid'
        );

        $this->addForeignKey(
            'fk-request-objectUuid',
            'request',
            'objectUuid',
            'object',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-taskUuid',
            'request',
            'taskUuid'
        );

        $this->createTable('{{%shutdown}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'startDate' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'endDate' => $this->dateTime(),
            'comment' => $this->string()->notNull(),
            'contragentUuid' => $this->string(45)->notNull(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-contragentUuid',
            'shutdown',
            'contragentUuid'
        );

        $this->addForeignKey(
            'fk-shutdown-contragentUuid',
            'shutdown',
            'contragentUuid',
            'contragent',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%task_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%task_verdict}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%task_template}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'oid' => $this->string(45)->notNull(),
            'description' => $this->string()->notNull(),
            'normative' => $this->integer()->defaultValue(120),
            'taskTypeUuid' => $this->string(45)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-taskTypeUuid',
            'task_template',
            'taskTypeUuid'
        );

        $this->addForeignKey(
            'fk-task_template-taskTypeUuid',
            'task_template',
            'taskTypeUuid',
            'task_type',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%task_type_tree}}', [
            '_id' => $this->primaryKey(),
            'parent' => $this->integer()->notNull(),
            'child' => $this->integer()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-parent',
            'task_type_tree',
            'parent'
        );

        $this->addForeignKey(
            'fk-task_type_tree-parent',
            'task_type_tree',
            'parent',
            'task_type',
            '_id',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-child',
            'task_type_tree',
            'child'
        );

        $this->addForeignKey(
            'fk-task_type_tree-child',
            'task_type_tree',
            'child',
            'task_type',
            '_id',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%work_status}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('{{%task}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'comment' => $this->string(),
            'equipmentUuid' => $this->string()->notNull(),
            'workStatusUuid' => $this->string()->notNull(),
            'taskVerdictUuid' => $this->string()->notNull(),
            'taskTemplateUuid' => $this->string()->notNull(),
            'date' => $this->dateTime()->notNull()->defaultValue('2019-01-01'),
            'startDate' => $this->dateTime()->notNull()->defaultValue('2019-01-01'),
            'endDate' => $this->dateTime()->notNull()->defaultValue('2019-01-01'),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-taskVerdictUuid',
            'task',
            'taskVerdictUuid'
        );

        $this->addForeignKey(
            'fk-operation-taskVerdictUuid',
            'task',
            'taskVerdictUuid',
            'task_verdict',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-workStatusUuid',
            'task',
            'workStatusUuid'
        );

        $this->addForeignKey(
            'fk-task-workStatusUuid',
            'task',
            'workStatusUuid',
            'work_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-taskTemplateUuid',
            'task',
            'taskTemplateUuid'
        );

        $this->addForeignKey(
            'fk-task-taskTemplateUuid',
            'task',
            'taskTemplateUuid',
            'task_template',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-equipmentUuid',
            'task',
            'equipmentUuid'
        );

        $this->addForeignKey(
            'fk-task-equipmentUuid',
            'task',
            'equipmentUuid',
            'equipment',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%operation}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'taskUuid' => $this->string()->notNull(),
            'workStatusUuid' => $this->string()->notNull(),
            'operationTemplateUuid' => $this->string()->notNull(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-taskUuid',
            'operation',
            'taskUuid'
        );

        $this->addForeignKey(
            'fk-operation-taskUuid',
            'operation',
            'taskUuid',
            'task',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-workStatusUuid',
            'operation',
            'workStatusUuid'
        );

        $this->addForeignKey(
            'fk-operation-workStatusUuid',
            'operation',
            'workStatusUuid',
            'work_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-operationTemplateUuid',
            'operation',
            'operationTemplateUuid'
        );

        $this->addForeignKey(
            'fk-operation-operationTemplateUuid',
            'operation',
            'operationTemplateUuid',
            'operation_template',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%task_operation}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'taskTemplateUuid' => $this->string()->notNull(),
            'operationTemplateUuid' => $this->string()->notNull(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-taskTemplateUuid',
            'task_operation',
            'taskTemplateUuid'
        );

        $this->addForeignKey(
            'fk-task_operation-taskTemplateUuid',
            'task_operation',
            'taskTemplateUuid',
            'task_template',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-operationTemplateUuid',
            'task_operation',
            'operationTemplateUuid'
        );

        $this->addForeignKey(
            'fk-task_operation-operationTemplateUuid',
            'task_operation',
            'operationTemplateUuid',
            'operation_template',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%task_user}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'taskUuid' => $this->string()->notNull(),
            'userUuid' => $this->string()->notNull(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-taskUuid',
            'task_user',
            'taskUuid'
        );

        $this->addForeignKey(
            'fk-task_user-taskUuid',
            'task_user',
            'taskUuid',
            'task_user',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userUuid',
            'task_user',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-task_user-userUuid',
            'task_user',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%user_house}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'houseUuid' => $this->string()->notNull(),
            'userUuid' => $this->string()->notNull(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-houseUuid',
            'user_house',
            'houseUuid'
        );

        $this->addForeignKey(
            'fk-user_house-houseUuid',
            'user_house',
            'houseUuid',
            'house',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userUuid',
            'user_house',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-user_house-userUuid',
            'user_house',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%user_system}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'oid' => $this->string(45)->notNull(),
            'equipmentSystemUuid' => $this->string()->notNull(),
            'userUuid' => $this->string()->notNull(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-equipmentSystemUuid',
            'user_system',
            'equipmentSystemUuid'
        );

        $this->addForeignKey(
            'fk-user_system-equipmentSystemUuid',
            'user_system',
            'equipmentSystemUuid',
            'house',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userUuid',
            'user_system',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-user_system-userUuid',
            'user_system',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );


        $this->createTable(self::USER_TOKEN, ['id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string(32)->notNull(),
            'valid_till' => $this->dateTime()->notNull(),
            'status' => $this->smallInteger(),
            'last_access' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),]);

        $this->addForeignKey(
            self::FK_USER_TOKEN2USER,
            self::USER_TOKEN,
            'user_id',
            self::USER,
            '_id',
            self::FK_CASCADE,
            self::FK_CASCADE
        );
        $this->addForeignKey(
            self::FK_USERS2USER,
            self::USERS,
            'user_id',
            self::USER,
            '_id',
            self::FK_RESTRICT,
            self::FK_CASCADE
        );
    }

    /**
     * {@inheritdoc}
     */
    public
    function safeDown()
    {

        return true;
    }
}
