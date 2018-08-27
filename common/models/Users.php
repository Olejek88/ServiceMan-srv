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
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 * @property integer $_id
 * @property string $uuid
 * @property string $name
 * @property string $login
 * @property string $pass
 * @property integer $type
 * @property string $tagId
 * @property integer $active
 * @property string $whoIs
 * @property integer $image
 * @property string $contact
 * @property integer $userId
 * @property integer $connectionDate
 * @property integer $createdAt
 * @property integer $changedAt
 */
class Users extends ActiveRecord
{
    private static $_IMAGE_ROOT = 'users';

    const USER_SYSTEM = "1111111-1111-1111-1111-111111111111";
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
                    'login',
                    'pass',
                    'type',
                    'tagId',
                    'active',
                    'whoIs',
                    'contact',
                    'userId'
                ],
                'required'
            ],
            [['type', 'active', 'userId'], 'integer'],
            [['image'], 'file'],
            [['connectionDate', 'createdAt', 'changedAt'], 'safe'],
            [['uuid', 'login', 'pass'], 'string', 'max' => 50],
            [['name', 'tagId', 'contact'], 'string', 'max' => 100],
            [['whoIs'], 'string', 'max' => 45],
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
            'name' => Yii::t('app', 'Имя'),
            'login' => Yii::t('app', 'Логин'),
            'pass' => Yii::t('app', 'Пароль'),
            'type' => Yii::t('app', 'Тип'),
            'tagId' => Yii::t('app', 'Tag ID'),
            'active' => Yii::t('app', 'Статус'),
            'whoIs' => Yii::t('app', 'Должность'),
            'image' => Yii::t('app', 'Фотография'),
            'contact' => Yii::t('app', 'Контакт'),
            'userId' => Yii::t('app', 'User id'),
            'connectionDate' => Yii::t('app', 'Дата подключения'),
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
            '_id',
            'uuid',
            'name',
            'login',
            'pass',
            'type',
            'tagId',
            'active',
            'whoIs',
            'image',
            'contact',
            'connectionDate',
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
        return $this->hasOne(User::className(), ['id' => 'userId']);
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
        return $this['_id'];
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
        $this->active = $this->active == 0 ? false : true;
        parent::afterFind();
    }

    /**
     * URL изображения.
     *
     * @return string | null
     */
    public function getImageUrl()
    {
        $dbName = \Yii::$app->session->get('user.dbname');

        $localPath = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . '/' . $this->image;
        if (file_exists(Yii::getAlias($localPath))) {
            $userName = \Yii::$app->user->identity->username;
            $dir = 'storage/' . $userName . '/' . self::$_IMAGE_ROOT . '/'
                . '/' . $this->image;
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
    public function getImageDir()
    {
        $dbName = \Yii::$app->session->get('user.dbname');
        $dir = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/';
        return $dir;
    }
}
