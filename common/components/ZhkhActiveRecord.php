<?php

namespace common\components;

use common\models\Users;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\Application;

class ZhkhActiveRecord extends ActiveRecord
{

    /**
     * @return object|ActiveQuery
     * @throws InvalidConfigException
     */
    public static function find()
    {
        if (Yii::$app instanceof Application) {
            $aq = Yii::createObject(ActiveQuery::class, [Users::class]);
            $users = $aq->where(['user_id' => Yii::$app->user->id])->one();
            $aq = Yii::createObject(ZhkhActiveQuery::class, [get_called_class()]);
            if ($users) {
                $aq->andWhere(['oid' => $users->oid]);
            } else {
                $aq->andWhere(['oid' => -1]);
            }
        } else {
            $aq = Yii::createObject(ActiveQuery::class, [get_called_class()]);
        }

        return $aq;
    }

    /**
     * Проверка на принадлежность пользователя указанному идентификатору организации.
     *
     * @param $attr
     * @param $param
     */
    public function checkOrganizationOwn($attr, $param)
    {
        if (Yii::$app instanceof Application) {
            if ($this->attributes[$attr] != Users::getOid(Yii::$app->user->identity)) {
                $this->addError($attr, 'Не верный идентификатор организации.');
            }
        } else {
            // TODO: как проверить что создаваемая запись принадлежит той организации которой она должна принадлежать?
        }
    }
}