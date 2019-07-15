<?php

use yii\db\Migration;
use common\models\Documentation;
use common\models\Organization;

/**
 * Class m190715_111549_fix_documentation_org
 */
class m190715_111549_fix_documentation_org extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%documentation}}', 'oid', $this->string(45)->notNull());
        $items = Documentation::find()->all();
        foreach ($items as $item) {
            $item->oid = Organization::ORG_SERVICE_UUID;
            $item->save();
        }

        $this->addForeignKey(
            'fk-documentation-organization-oid',
            '{{%documentation}}',
            'oid',
            '{{%organization}}',
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
        echo "m190715_111549_fix_documentation_org cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_111549_fix_documentation_org cannot be reverted.\n";

        return false;
    }
    */
}
