<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "object_contragent".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $objectUuid
 * @property string $contragentUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class ObjectContragent extends ActiveRecord
{
    /**
     * Название таблицы.
     * @return string
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'object_contragent';
    }

    /**
     * Rules.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'objectUuid', 'contragentUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'objectUuid', 'contragentUuid'], 'string', 'max' => 50],
        ];
    }

    /**
     * Labels.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'object' => Yii::t('app', 'Объект'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'contragent' => Yii::t('app', 'Контрагент'),
            'contragentUuid' => Yii::t('app', 'Контрагент'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContragent()
    {
        return $this->hasOne(Contragent::class, ['uuid' => 'contragentUuid']);
    }

    /**
     * Объект связанного поля.
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::class, ['uuid' => 'objectUuid']);
    }

}
