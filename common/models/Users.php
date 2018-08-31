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
 * Class Users
 *
 * @property integer $id
 * @property string $uuid
 * @property string $name
 * @property string $pin
 * @property string $contact
 * @property integer $user_id
 * @property integer $createdAt
 * @property integer $changedAt
 */

class Users extends ActiveRecord
{
    /**
     * Behaviors.
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
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'users';
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
                    'name',
                    'pin',
                    'contact'
                ],
                'required'
            ],
            [['user_id'], 'integer'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'pin'], 'string', 'max' => 50],
            [['name', 'contact'], 'string', 'max' => 100],
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
            'id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'name' => Yii::t('app', 'Имя'),
            'pin' => Yii::t('app', 'Пин код'),
            'contact' => Yii::t('app', 'Контакт'),
            'user_id' => Yii::t('app', 'User id'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'uuid',
            'name',
            'pin',
            'user_id',
            'contact',
            'user' => function ($model) {
                return $model->user;
            },
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * Связываем пользователей из yii с пользователями из toir.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
     * Поск объекта по имени пользователя.
     *
     * @param string $username Имя пользователя.
     *
     * @return null|static
     */
    public static function findByUsername($username)
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Возвращает id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this['id'];
    }

    /**
     * Что-то проверяет.
     *
     * @param array $User Пользователь.
     *
     * @return int
     */
    public function dataChecking($User)
    {
        if ($User === "") {
            $User = null;
        }
        $count = count($User);
        if ($count === 0 || $count > 1) {
            // В случае более одной строчек из таблицы user,
            // необходимо передать эту информацию администратору, для
            // устранения совподений в таблице.
            // В полученном маccиве, нуль или больше одного, выдаем Not object.

            // При необходимости создатиь ветку отправки данных с ошибкой
            // $postError = new Table();
            //     $postError->error = $dataUser;
            // $postError->save();

            return 0;
        } else {
            return $count;
        }
    }

    /**
     * Какие-то действия.
     *
     * @return void
     */
    public function afterFind()
    {
        parent::afterFind();
    }
}
