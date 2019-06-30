<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "request_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $taskTemplateUuid
 * @property string $createdAt
 * @property string $changedAt

 * @property TaskTemplate $taskTemplate
 */
class RequestType extends ActiveRecord
{
    const GENERAL = "E49AE9AD-3C31-42F8-A751-AAEB890C2190";
    const REQUEST_PAY = 1;
    const REQUEST_FREE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Название'),
            'taskTemplate' => Yii::t('app', 'Шаблон задачи'),
            'taskTemplateUuid' => Yii::t('app', 'Шаблон задачи'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getTaskTemplate()
    {
        return $this->hasOne(
            TaskTemplate::class, ['uuid' => 'taskTemplateUuid']
        );
    }
}
