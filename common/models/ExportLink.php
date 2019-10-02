<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "export_link".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $table
 * @property string $dbUuid
 * @property string $externalId
 * @property string $createdAt
 * @property string $changedAt
 */
class ExportLink extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Ссылки для экспорта';

    /**
     * Behaviors.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'export_link';
    }

    /**
     * Rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'externalId',
                    'dbUuid',
                    'table'
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'externalId',
                    'dbUuid',
                    'table',
                    'oid',
                ],
                'string', 'max' => 50
            ],
            [['oid'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => ['oid' => 'uuid']],
            [['oid'], 'checkOrganizationOwn'],
        ];
    }

    /**
     * Метки для свойств.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'dbUuid' => Yii::t('app', 'Uuid ID параметра в базе'),
            'table' => Yii::t('app', ' Таблица'),
            'externalId' => Yii::t('app', 'Внешний ИД'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
