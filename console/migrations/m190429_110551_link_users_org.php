<?php

use common\models\Organization;
use yii\db\Migration;

/**
 * Class m190429_110551_link_users_org
 */
class m190429_110551_link_users_org extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%organization}}', [
            '_id' => 1,
            'uuid' => Organization::ORG_SERVICE_UUID,
            'title' => 'Service organization',
            'inn' => '000000000000',
            'secret' => 'secret',
        ]);
        $this->addColumn('{{%users}}', 'oid', $this->string(45)->notNull());
        $this->update('{{%users}}', [
            'oid' => Organization::ORG_SERVICE_UUID,
        ], [
            '_id' => 1,
        ]);
        $this->update('{{%users}}', [
            'oid' => Organization::ORG_SERVICE_UUID,
        ], [
            '_id' => 2,
        ]);

        $this->addForeignKey(
            'fk-users-organization-oid',
            '{{%users}}',
            'oid',
            '{{%organization}}',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->alterColumn('{{%user_token}}', 'last_access', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('{{%user_token}}', 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('{{%user_token}}', 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190429_110551_link_users_org cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190429_110551_link_users_org cannot be reverted.\n";

        return false;
    }
    */
}
