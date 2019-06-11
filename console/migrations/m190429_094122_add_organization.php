<?php


/**
 * Class m190429_094122_add_organization
 */
class m190429_094122_add_organization extends \console\yii2\Migration
{

    private const TABLE_ORGANIZATION = '{{%organization}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable(self::TABLE_ORGANIZATION, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string(100)->notNull()->defaultValue(''),
            'inn' => $this->string(100)->notNull()->defaultValue(''),
            'secret' => $this->string(100)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // связываем все модели с организацией
        $this->addForeignKey(
            'fk-city-organization-oid',
            '{{%city}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-street-organization-oid',
            '{{%street}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-house_type-organization-oid',
            '{{%house_type}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-house-organization-oid',
            '{{%house}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-object_type-organization-oid',
            '{{%object_type}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-object-organization-oid',
            '{{%object}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-alarm-organization-oid',
            '{{%alarm}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-contragent-organization-oid',
            '{{%contragent}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-contragent_register-organization-oid',
            '{{%contragent_register}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-equipment-organization-oid',
            '{{%equipment}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-equipment_register-organization-oid',
            '{{%equipment_register}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-export_link-organization-oid',
            '{{%export_link}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-measure-organization-oid',
            '{{%measure}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-messages-organization-oid',
            '{{%messages}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-object_contragent-organization-oid',
            '{{%object_contragent}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-operation_template-organization-oid',
            '{{%operation_template}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-request-organization-oid',
            '{{%request}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-shutdown-organization-oid',
            '{{%shutdown}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-task_template-organization-oid',
            '{{%task_template}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-task-organization-oid',
            '{{%task}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-operation-organization-oid',
            '{{%operation}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-task_user-organization-oid',
            '{{%task_user}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_house-organization-oid',
            '{{%user_house}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_system-organization-oid',
            '{{%user_system}}',
            'oid',
            self::TABLE_ORGANIZATION,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190429_094122_add_organization cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190429_094122_add_organization cannot be reverted.\n";

        return false;
    }
    */
}
