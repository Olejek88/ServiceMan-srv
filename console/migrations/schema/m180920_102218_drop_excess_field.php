<?php

use \common\models\Gpstrack;

/**
 * Class m180920_102218_drop_excess_field
 */
class m180920_102218_drop_excess_field extends \console\yii2\Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->dropColumn(Gpstrack::tableName(), 'uuid');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m180920_102218_drop_excess_field cannot be reverted.\n";

        return false;
    }

}
