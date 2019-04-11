<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Contragent model
 *
 * @property integer $_id
 * @property string $title
 * @property string $address
 * @property string $phone
 * @property string $inn
 * @property string $director
 * @property string $email
 * @property string $contragentTypeUuid
 * @property integer $status
 * @property string $createdAt
 * @property string $changedAt
 *
 *
 */
class Contragent extends ActiveRecord implements IdentityInterface
{
    /**
     * Table name.
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%contragent}}';
    }

    /**
     * Behaviors.
     *
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Rules.
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => Status::STATUS_DEFAULT],
            ['status', 'in', 'range' => [Status::STATUS_DEFAULT, Status::STATUS_ARCHIVED]],
        ];
    }

    /**
     * Поиск контрагента по id и статусу.
     *
     * @param integer $id Ид пользователя.
     *
     * @inheritdoc
     *
     * @return Contragent
     */
    public static function findIdentity($id)
    {
        return static::findOne(['_id' => $id, 'status' => Status::STATUS_DEFAULT]);
    }
}
