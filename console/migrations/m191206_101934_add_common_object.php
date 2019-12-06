<?php

use common\components\ReferenceFunctions;
use common\models\Organization;
use yii\base\InvalidConfigException;
use yii\db\Migration;

/**
 * Class m191206_101934_add_common_object
 */
class m191206_101934_add_common_object extends Migration
{
    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        Yii::$app->set('db', $this->db);
        $organisations = Organization::find()->all();
        foreach ($organisations as $organisation) {
            if ($organisation['uuid'] != Organization::ORG_SERVICE_UUID)
                ReferenceFunctions::addCommonObject($organisation['uuid'], $this->db);
        }

        // делаем индексированными поля gis_id для увеличения скорости выборки данных
        $this->createIndex('city-gis_id-idx', '{{%city}}', 'gis_id');
        $this->createIndex('street-gis_id-idx', '{{%street}}', 'gis_id');
        $this->createIndex('house-gis_id-idx', '{{%house}}', 'gis_id');
        $this->createIndex('object-gis_id-idx', '{{%object}}', 'gis_id');
        $this->createIndex('contragent-gis_id-idx', '{{%contragent}}', 'gis_id');
        $this->createIndex('house_type-gis_id-idx', '{{%house_type}}', 'gis_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191206_101934_add_common_object cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191206_101934_add_common_object cannot be reverted.\n";

        return false;
    }
    */
}
