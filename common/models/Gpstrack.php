<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "gpstrack".
 *
 * @property string $userUuid
 * @property double $latitude
 * @property double $longitude
 * @property string $date
 */
class Gpstrack extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gpstrack';
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
