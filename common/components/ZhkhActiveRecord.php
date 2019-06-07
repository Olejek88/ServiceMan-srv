<?php


namespace common\components;

use common\models\Users;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class ZhkhActiveRecord extends ActiveRecord
{

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public static function find()
    {
        $aq = Yii::createObject(ActiveQuery::class, [Users::class]);
        $users = $aq->where(['user_id' => Yii::$app->user->id])->one();
        $aq = Yii::createObject(ZhkhActiveQuery::class, [get_called_class()]);
        if ($users) {
            $aq->andWhere(['oid' => $users->oid]);
        } else {
            $aq->andWhere(['oid' => -1]);
        }

        return $aq;
    }
}