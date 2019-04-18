<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "gps_track".
 *
 * @property string $userUuid
 * @property string $oid идентификатор организации
 * @property double $latitude
 * @property double $longitude
 * @property string $date
 * @property int $_id
 * @property string $uuid
 * @property bool $sent
 * @property int $changedAt
 * @property int $createdAt
 */
class Gpstrack extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gps_track}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userUuid', 'latitude', 'longitude'], 'required'],
            [['latitude', 'longitude'], 'number'],
            [['date'], 'safe'],
            [['userUuid'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userUuid' => Yii::t('app', 'Uuid Пользователя'),
            'latitude' => Yii::t('app', 'Широта'),
            'longitude' => Yii::t('app', 'Долгота'),
            'date' => Yii::t('app', 'Дата'),
        ];
    }
}
