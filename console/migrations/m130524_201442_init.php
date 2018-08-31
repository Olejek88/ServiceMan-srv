<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%city}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%street}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'cityUuid' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
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
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%flat_status}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%house}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'number' => $this->string()->notNull(),
            'houseStatusUuid' => $this->string()->notNull(),
            'streetUuid' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
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

        $this->createTable('{{%flat}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'number' => $this->string()->notNull(),
            'flatStatusUuid' => $this->string()->notNull(),
            'houseUuid' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-houseUuid',
            'flat',
            'houseUuid'
        );

        $this->addForeignKey(
            'fk-flat-houseUuid',
            'flat',
            'houseUuid',
            'house',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-flatStatusUuid',
            'flat',
            'flatStatusUuid'
        );

        $this->addForeignKey(
            'fk-flat-flatStatusUuid',
            'flat',
            'flatStatusUuid',
            'flat_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%resident}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'owner' => $this->string()->notNull(),
            'flatUuid' => $this->string()->notNull(),
            'inn' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-flatUuid',
            'resident',
            'flatUuid'
        );

        $this->addForeignKey(
            'fk-resident-flatUuid',
            'resident',
            'flatUuid',
            'flat',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%subject}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'contractNumber' => $this->string()->unique()->notNull(),
            'contractDate' => $this->date()->notNull(),
            'houseUuid' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-houseUuid',
            'subject',
            'houseUuid'
        );

        $this->addForeignKey(
            'fk-subject-houseUuid',
            'subject',
            'houseUuid',
            'house',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%alarm_type}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%alarm_status}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'user_id' => $this->integer()->notNull()->unique(),
            'name' => $this->string()->notNull(),
            'pin' => $this->string()->notNull(),
            'image' => $this->string(),
            'contact' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%alarm}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'alarmTypeUuid' => $this->string()->notNull(),
            'alarmStatusUuid' => $this->string()->notNull(),
            'userUuid' => $this->string()->notNull(),
            'longitude' => $this->double(),
            'latitude' => $this->double(),
            'comment' => $this->string()->notNull(),
            'date' => $this->date()->notNull()
        ], $tableOptions);

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
            'idx-userUuid',
            'alarm',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-user-userUuid',
            'alarm',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%equipment_type}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%equipment_status}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%equipment}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'houseUuid' => $this->string()->notNull(),
            'flatUuid' => $this->string()->notNull(),
            'equipmentTypeUuid' => $this->string()->notNull(),
            'equipmentStatusUuid' => $this->string()->notNull(),
            'serial' => $this->string()->notNull(),
            'testDate' => $this->date()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-houseUuid',
            'equipment',
            'houseUuid'
        );

        $this->addForeignKey(
            'fk-equipment-houseUuid',
            'equipment',
            'houseUuid',
            'house',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-flatUuid',
            'equipment',
            'flatUuid'
        );

        $this->addForeignKey(
            'fk-equipment-flatUuid',
            'equipment',
            'flatUuid',
            'flat',
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

        $this->createTable('{{%control_point_type}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%photo_house}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'houseUuid' => $this->string()->notNull(),
            'userUuid' => $this->string()->notNull(),
            'longitude' => $this->double(),
            'latitude' => $this->double(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-houseUuid',
            'photo_house',
            'houseUuid'
        );

        $this->addForeignKey(
            'fk-photoHouse-houseUuid',
            'photo_house',
            'houseUuid',
            'house',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userUuid',
            'photo_house',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-photoHouse-userUuid',
            'photo_house',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%photo_flat}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'flatUuid' => $this->string()->notNull(),
            'userUuid' => $this->string()->notNull(),
            'longitude' => $this->double(),
            'latitude' => $this->double(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-flatUuid',
            'photo_flat',
            'flatUuid'
        );

        $this->addForeignKey(
            'fk-photoFlat-flatUuid',
            'photo_flat',
            'flatUuid',
            'flat',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userUuid',
            'photo_flat',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-photoFlat-userUuid',
            'photo_flat',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%photo_equipment}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'equipmentUuid' => $this->string()->notNull(),
            'userUuid' => $this->string()->notNull(),
            'longitude' => $this->double(),
            'latitude' => $this->double(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-equipmentUuid',
            'photo_equipment',
            'equipmentUuid'
        );

        $this->addForeignKey(
            'fk-photoEquipment-equipmentUuid',
            'photo_equipment',
            'equipmentUuid',
            'equipment',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userUuid',
            'photo_equipment',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-photoEquipment-userUuid',
            'photo_equipment',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%photo_alarm}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'alarmUuid' => $this->string()->notNull(),
            'userUuid' => $this->string()->notNull(),
            'longitude' => $this->double(),
            'latitude' => $this->double(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-alarmUuid',
            'photo_alarm',
            'alarmUuid'
        );

        $this->addForeignKey(
            'fk-photoAlarm-alarmUuid',
            'photo_alarm',
            'alarmUuid',
            'alarm',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userUuid',
            'photo_alarm',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-photoAlarm-userUuid',
            'photo_alarm',
            'userUuid',
            'users',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%measure}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'equipmentUuid' => $this->string()->notNull(),
            'userUuid' => $this->string()->notNull(),
            'value' => $this->double(),
            'date' => $this->date()->notNull(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
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

        $this->createTable('{{%gps_track}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'userUuid' => $this->string()->notNull(),
            'date' => $this->date()->notNull(),
            'longitude' => $this->double(),
            'latitude' => $this->double(),
            'sent' => $this->boolean(),
            'createdAt' => $this->integer()->notNull(),
            'changedAt' => $this->integer()->notNull(),
        ], $tableOptions);

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
    }

    public function down()
    {
/*        $this->dropTable('{{%user}}');
        $this->dropTable('{{%city}}');
        $this->dropTable('{{%street}}');
        $this->dropTable('{{%house_status}}');
        $this->dropTable('{{%flat_status}}');
        $this->dropTable('{{%house}}');
        $this->dropTable('{{%flat}}');
        $this->dropTable('{{%resident}}');
        $this->dropTable('{{%subject}}');
        $this->dropTable('{{%alarm_type}}');
        $this->dropTable('{{%alarm_status}}');
        $this->dropTable('{{%alarm}}');
        $this->dropTable('{{%equipment}}');
        $this->dropTable('{{%photo_house}}');
        $this->dropTable('{{%photo_flat}}');
        $this->dropTable('{{%photo_equipment}}');
        $this->dropTable('{{%photo_alarm}}');
        $this->dropTable('{{%measure}}');
        $this->dropTable('{{%gps_track}}');*/
     return true;
    }
}
