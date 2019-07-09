<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "contragent_register".
 *
 * @property int $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $contragentUuid
 * @property string $date
 * @property string $description
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Contragent $contragent
 */
class ContragentRegister extends ZhkhActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contragent_register';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'contragentUuid', 'description', 'date'], 'required'],
            [['data', 'oid'], 'safe'],
            [['uuid', 'contragentUuid', 'date'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 350],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
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
            'uuid' => Yii::t('app', 'Uuid'),
            'contragentUuid' => Yii::t('app', 'Контрагент'),
            'date' => Yii::t('app', 'Дата'),
            'description' => Yii::t('app', 'Описание'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getContragent()
    {
        return $this->hasOne(Contragent::class, ['uuid' => 'contragentUuid']);
    }


    public function fields()
    {
        return ['uuid',
            'contragent' => function ($model) {
                return $model->contragent;
            }, 'date', 'description', 'createdAt', 'changedAt'
        ];
    }
}