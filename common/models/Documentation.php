<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "documentation".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $equipmentUuid
 * @property string $documentationTypeUuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 * @property string $path
 * @property string $equipmentTypeUuid
 *
 * @property Equipment $equipment
 * @property string $docDir
 * @property string $docUrl
 * @property DocumentationType $documentationType
 * @property EquipmentType $equipmentType
 */

class Documentation extends ActiveRecord
{
    private static $_IMAGE_ROOT = 'doc';

    /**
     * Behaviors
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
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'documentation';
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
            [['uuid', 'title', 'documentationTypeUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [
                ['path'],
                'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg,  pdf, doc, docx, txt',
                'maxSize' => 1024 * 1024 * 10
            ],
            [
                [
                    'uuid',
                    'equipmentUuid',
                    'documentationTypeUuid',
                    'equipmentTypeUuid'
                ],
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
        return ['_id','uuid', 'equipmentUuid',
            'equipment' => function ($model) {
                return $model->equipment;
            },
            'documentationTypeUuid',
            'documentationType' => function ($model) {
                return $model->documentationType;
            }, 'title','path',
            'createdAt', 'changedAt',
            'equipmentTypeUuid',
            'equipmentType' => function ($model) {
                return $model->equipmentType;
            },
        ];
    }

    /**
     * Названия атрибутов
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
            'equipmentUuid' => Yii::t('app', 'Оборудование'),
            'equipment' => Yii::t('app', 'Оборудование'),
            'equipmentTypeUuid' => Yii::t('app', 'Тип оборудования'),
            'equipmentType' => Yii::t('app', 'Тип оборудования'),
            'documentationTypeUuid' => Yii::t('app', 'Тип документации'),
            'documentationType' => Yii::t('app', 'Тип документации'),
            'title' => Yii::t('app', 'Название'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
            'path' => Yii::t('app', 'Путь'),
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
    public function getEquipmentType()
    {
        return $this->hasOne(
            EquipmentType::class, ['uuid' => 'equipmentTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentationType()
    {
        return $this->hasOne(
            DocumentationType::class, ['uuid' => 'documentationTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['uuid' => 'equipmentUuid']);
    }

    /**
     * URL изображения.
     *
     * @return string | null
     */
    public function getDocUrl()
    {
        $dbName = \Yii::$app->session->get('user.dbname');
        if ($this->equipmentTypeUuid != null) {
            $typeUuid = $this->equipmentTypeUuid;
        } else if ($this->equipment->equipmentTypeUuid != null) {
            $typeUuid = $this->equipment->equipmentTypeUuid;
        } else {
            return null;
        }

        $localPath = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/' . $this->path;
        if (file_exists(Yii::getAlias($localPath))) {
            $userName = \Yii::$app->user->identity->username;
            $dir = 'storage/' . $userName . '/' . self::$_IMAGE_ROOT . '/'
                . $typeUuid . '/' . $this->path;
            $url = Yii::$app->request->BaseUrl . '/' . $dir;
        } else {
            // такого в штатном режиме быть не должно!
            $url = null;
        }

        return $url;
    }

    /**
     * Возвращает каталог в котором должен находится файл изображения,
     * относительно папки web.
     *
     * @return string
     */
    public function getDocDir()
    {
        if ($this->equipmentTypeUuid != null) {
            $typeUuid = $this->equipmentTypeUuid;
        } else if ($this->equipment->equipmentTypeUuid != null) {
            $typeUuid = $this->equipment->equipmentTypeUuid;
        } else {
            return null;
        }

        $dbName = \Yii::$app->session->get('user.dbname');
        $dir = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/';
        return $dir;
    }

    /**
     * Возвращает каталог в котором должен находится файл изображения,
     * относительно папки web.
     *
     * @param string $typeUuid Uuid типа оборудования
     *
     * @return string
     */
    public function getDocDirType($typeUuid)
    {
        $dbName = \Yii::$app->session->get('user.dbname');
        $dir = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/';
        return $dir;
    }
}
