<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "objects".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $objectTypeUuid
 * @property string $parentUuid
 * @property string $title
 * @property string $description
 * @property string $photo
 * @property double $latitude
 * @property double $longitude
 * @property string $createdAt
 * @property string $changedAt
 */
class Objects extends ActiveRecord
{
    private static $_IMAGE_ROOT = 'object';

    /**
     * Behaviors
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'objects';
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'objectTypeUuid',
                    'parentUuid',
                    'title',
                    'description',
//                    'latitude',
//                    'longitude'
                ],
                'required'
            ],
            [['description'], 'string'],
            [['photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['latitude', 'longitude'], 'number'],
            [['uuid', 'objectTypeUuid', 'parentUuid'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
            [['createdAt', 'changedAt'], 'safe'],
        ];
    }

    /**
     * Названия отрибутов
     *
     * @inheritdoc
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'objectTypeUuid' => Yii::t('app', 'UUID типа объекта'),
            'objectType' => Yii::t('app', 'Тип объекта'),
            'parentUuid' => Yii::t('app', 'Parent Uuid'),
            'parent' => Yii::t('app', 'Parent'),
            'title' => Yii::t('app', 'Название'),
            'description' => Yii::t('app', 'Описание'),
            'photo' => Yii::t('app', 'Фотография'),
            'latitude' => Yii::t('app', 'Широта'),
            'longitude' => Yii::t('app', 'Долгота'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id','uuid',
            'objectType' => function ($model) {
                return $model->objectType;
            },
            'parent' => function ($model) {
                return $model->parent;
            },
            'title',
            'description',
            'photo',
            'latitude',
            'longitude',
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * Проверка целостности модели?
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectType()
    {
        return $this->hasOne(ObjectType::className(), ['uuid' => 'objectTypeUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Objects::className(), ['uuid' => 'parentUuid']);
    }

    /**
     * URL изображения.
     *
     * @return string
     */
    public function getPhotoUrl()
    {
        $noImage = '/storage/order-level/no-image-icon-4.png';

        if ($this->photo == '') {
            return $noImage;
        }

        $dbName = \Yii::$app->session->get('user.dbname');
        $localPath = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $this->photo;
        if (file_exists(Yii::getAlias($localPath))) {
            $userName = \Yii::$app->user->identity->username;
            $dir = 'storage/' . $userName . '/' . self::$_IMAGE_ROOT . '/'
                . '/' . $this->photo;
            $url = Yii::$app->request->BaseUrl . '/' . $dir;
        } else {
            $url = $noImage;
        }

        return $url;
    }

    /**
     * Возвращает каталог в котором должен находится файл изображения,
     * относительно папки web.
     *
     * @return string
     */
    public function getPhotoDir()
    {
        $dbName = \Yii::$app->session->get('user.dbname');
        $dir = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/';
        return $dir;
    }
}
