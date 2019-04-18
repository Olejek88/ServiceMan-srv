<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Contragent model
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $gis_id глобальный идентификатор в ГИС ЖКХ
 * @property string $title
 * @property string $address
 * @property string $phone
 * @property string $inn
 * @property string $director
 * @property string $email
 * @property string $contragentTypeUuid
 * @property integer $deleted
 * @property string $createdAt
 * @property string $changedAt
 *
 */
class Contragent extends ActiveRecord
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
            ['deleted', 'default', 'value' => Status::STATUS_DEFAULT],
            ['deleted', 'in', 'range' => [Status::STATUS_DEFAULT, Status::STATUS_ARCHIVED]],
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
        return static::findOne(['_id' => $id, 'deleted' => Status::STATUS_DEFAULT]);
    }
}
