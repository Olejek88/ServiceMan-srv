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
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stage_template".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $description
 * @property string $image
 * @property string $title
 * @property integer $normative
 * @property string $stageTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class StageTemplate extends ActiveRecord
{
    private static $_IMAGE_ROOT = 'stype';

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
        return 'stage_template';
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return mixed
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'description',
                    'title',
                    'normative',
                    'stageTypeUuid',
                ],
                'required'
            ],
            [['description'], 'string'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['normative'], 'integer'],
            [['createdAt', 'changedAt'], 'safe'],
            [
                ['uuid', 'stageTypeUuid'],
                'string', 'max' => 45
            ],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return [
            '_id',
            'uuid',
            'title',
            'description',
            'image',
            'stageTypeUuid',
            'normative',
            'stageType' => function ($model) {
                return $model->stageType;
            }, 'createdAt', 'changedAt'
        ];
    }

    /**
     * Attribute labels
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
            'description' => Yii::t('app', 'Описание'),
            'image' => Yii::t('app', 'Фотография'),
            'title' => Yii::t('app', 'Название'),
            'normative' => Yii::t('app', 'Норматив'),
            'stageTypeUuid' => Yii::t('app', 'Тип шаблона этапа'),
            'stageType' => Yii::t('app', 'Тип шаблона этапа'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Upload
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
     * Link
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStageType()
    {
        return $this->hasOne(
            StageType::className(), ['uuid' => 'stageTypeUuid']
        );
    }

    /**
     * URL изображения.
     *
     * @return string
     */
    public function getImageUrl()
    {
        $noImage = '/storage/order-level/no-image-icon-4.png';

        if ($this->image == '') {
            return $noImage;
        }

        $dbName = \Yii::$app->session->get('user.dbname');
        $typeUuid = $this->stageTypeUuid;
        $localPath = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/' . $this->image;
        if (file_exists(Yii::getAlias($localPath))) {
            $userName = \Yii::$app->user->identity->username;
            $dir = 'storage/' . $userName . '/' . self::$_IMAGE_ROOT . '/'
                . $typeUuid . '/' . $this->image;
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
    public function getImageDir()
    {
        $typeUuid = $this->stageTypeUuid;
        $dbName = \Yii::$app->session->get('user.dbname');
        $dir = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/';
        return $dir;
    }

    /**
     * Возвращает каталог в котором должен находится файл изображения,
     * относительно папки web.
     *
     * @param string $typeUuid Uuid типа операции
     *
     * @return string
     */
    public function getImageDirType($typeUuid)
    {
        $dbName = \Yii::$app->session->get('user.dbname');
        $dir = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/';
        return $dir;
    }
}
