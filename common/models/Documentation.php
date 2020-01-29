<?php

namespace common\models;

use common\components\ZhkhActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "documentation".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid
 * @property string $equipmentUuid
 * @property string $documentationTypeUuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 * @property string $path
 * @property string $equipmentTypeUuid
 * @property string $houseUuid
 *
 * @property Equipment $equipment
 * @property string $docDir
 * @property DocumentationType $documentationType
 * @property Organization $organization
 * @property EquipmentType $equipmentType
 *
 * @property string $docLocalPath
 * @property string $docFullPath
 * @property string $fileLocalDir
 * @property House $house
 * @property string $fileFullDir
 */
class Documentation extends ZhkhActiveRecord
{
    public const DESCRIPTION = 'Документация';

    private static $_FILE_ROOT_DIR = 'doc';
    public $docFile;
    public $entityType;

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
            [['path'], 'safe'],
            [['docFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg,  pdf, doc, docx, txt',
                'maxSize' => 1024 * 1024 * 10,
            ],
            [
                [
                    'uuid',
                    'oid',
                    'equipmentUuid',
                    'documentationTypeUuid',
                    'equipmentTypeUuid',
                    'houseUuid',
                    'entityType'
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
        $fields = parent::fields();
        return $fields;
//        return ['_id','uuid', 'equipmentUuid',
//            'equipment' => function ($model) {
//                return $model->equipment;
//            },
//            'documentationTypeUuid',
//            'documentationType' => function ($model) {
//                return $model->documentationType;
//            }, 'title','path',
//            'createdAt', 'changedAt',
//            'equipmentTypeUuid',
//            'equipmentType' => function ($model) {
//                return $model->equipmentType;
//            },
//        ];
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
            'houseUuid' => Yii::t('app', 'Дом'),
            'house' => Yii::t('app', 'Дом'),
            'title' => Yii::t('app', 'Название'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
            'path' => Yii::t('app', 'Путь'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
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
     * @return ActiveQuery
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
     * @return ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['uuid' => 'equipmentUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getHouse()
    {
        return $this->hasOne(House::class, ['uuid' => 'houseUuid']);
    }

    /**
     * Возвращает каталог в котором должен находится файл изображения,
     * относительно папки web.
     *
     * @return string
     */
    public function getDocDir()
    {
        /* if ($this->equipmentTypeUuid != null) {
            $typeUuid = $this->equipmentTypeUuid;
        } else if ($this->equipment->equipmentTypeUuid != null) {
            $typeUuid = $this->equipment->equipmentTypeUuid;
        } else {
            return null;
        }*/
        // валим все в одну папку - сомневаюсь, что будет много
        $dir = 'storage/doc/';
        return $dir;
    }

    /**
     * fetch stored image file name with complete path
     * @return string
     */
    public function getDocFullPath()
    {
        return Yii::getAlias('@backend/web/' . $this->getFileLocalDir() . '/' . $this->path);
    }

    /**
     * fetch stored image file name with complete path
     * @return string
     */
    public function getDocLocalPath()
    {
        return $this->getFileLocalDir() . '/' . $this->path;
    }

    /**
     *
     */
    public function getFileLocalDir()
    {
        /** @var User $identity */
        $identity = Yii::$app->user->identity;
        $users = $identity->users;
        $org = $users->organization;

        $modelDir = '';
        if ($this->equipmentUuid != null) {
            $modelDir = $this->equipmentUuid;
        } else if ($this->equipmentTypeUuid != null) {
            $modelDir = $this->equipmentTypeUuid;
        }

        return 'storage/' . $org->_id . '/files/' . self::$_FILE_ROOT_DIR . '/' . $modelDir;
    }

    /**
     *
     */
    public function getFileFullDir()
    {
        return Yii::getAlias('@backend/web/' . $this->getFileLocalDir());
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
        // валим все в одну папку - сомневаюсь, что будет много
        $dir = 'storage/doc/';
        return $dir;
    }

    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['uuid' => 'oid']);
    }

    /**
     * Process upload of image
     *
     * @param $fieldName
     * @return mixed the uploaded image instance
     */
    public function uploadDocFile($fieldName)
    {
        // get the uploaded file instance. for multiple file uploads
        // the following data will return an array (you may need to use
        // getInstances method)
        $uploadFile = UploadedFile::getInstance($this, $fieldName);

        // if no image was uploaded abort the upload
        if (empty($uploadFile)) {
            return false;
        }

        $res = explode(".", $uploadFile->name);
        $ext = end($res);

        // generate a unique file name
        $this->path = $this->uuid . ".{$ext}";

        // the uploaded image instance
        return $uploadFile;
    }

    function getActionPermissions()
    {
        return array_merge_recursive(parent::getActionPermissions(), [
            'read' => [
                'add',
            ],
            'edit' => [
                'save',
            ]]);
    }
}
