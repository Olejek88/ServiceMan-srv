<?php

namespace common\components;

use common\models\IPermission;
use common\models\User;
use common\models\Users;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\Application;

/**
 *
 * @property array $actionPermissions
 * @property array $permissions
 */
class ZhkhActiveRecord extends ActiveRecord implements IPermission
{
    const SCENARIO_UPDATE = 'update';

    /**
     * @return object|ActiveQuery
     * @throws InvalidConfigException
     * @throws Exception
     */
    public static function find()
    {
        /** @var ActiveRecord $calledClass */
        $calledClass = get_called_class();
        $aq = Yii::createObject(ZhkhActiveQuery::class, [$calledClass]);
        if (Yii::$app instanceof Application) {
            if (!Yii::$app->user->isGuest) {
                /** @var User $identity */
                $identity = Yii::$app->user->identity;
                if ($identity->username != 'sUser') {
                    // обычный пользователь
                    $oid = Yii::$app->db
                        ->createCommand('SELECT oid FROM users WHERE user_id = ' . $identity->id)
                        ->query()
                        ->read();
                    $aq->andWhere([$calledClass::tablename() . '.oid' => $oid['oid']]);
                }
            }
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
            if ($this->attributes[$attr] != Users::getCurrentOid()) {
                $this->addError($attr, 'Не верный идентификатор организации.');
            }
        }
    }

    function getPermissions()
    {
        return [
            'read' => 'Чтение',
            'edit' => 'Редактирование',
        ];
    }

    function getActionPermissions()
    {
        return [
            'read' => [
                'index',
                'view',
            ],
            'edit' => [
                'create',
                'update',
                'delete',
            ],
        ];
    }
}