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
 * This is the model class for table "task_template".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property string $image
 * @property integer $normative
 * @property string $taskTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class TaskTemplate extends ActiveRecord
{
    private static $_IMAGE_ROOT = 'ttype';

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
     * Имя таблицы.
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'task_template';
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
                    'title',
                    'description',
                    'normative',
                    'taskTypeUuid'
                ],
                'required',
            ],
            [['description'], 'string'],
            [['normative'], 'filter', 'filter' => 'intval'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'taskTypeUuid'], 'string', 'max' => 45],
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
            '_id', 'uuid', 'title', 'description', 'image', 'taskTypeUuid',
            'normative',
            'taskType' => function ($model) {
                return $model->taskType;
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
            'title' => Yii::t('app', 'Название'),
            'description' => Yii::t('app', 'Описание'),
            'image' => Yii::t('app', 'Фотография'),
            'normative' => Yii::t('app', 'Норматив'),
            'taskTypeUuid' => Yii::t('app', 'Uuid типа задачи'),
            'taskType' => Yii::t('app', 'Тип задачи'),
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
     * @return \yii\db\ActiveQuery | \common\models\TaskType
     */
    public function getTaskType()
    {
        return $this->hasOne(TaskType::className(), ['uuid' => 'taskTypeUuid']);
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
        $typeUuid = $this->taskTypeUuid;
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
        $typeUuid = $this->taskTypeUuid;
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
