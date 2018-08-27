<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Api\controllers
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace api\controllers;

use common\components\MainFunctions;
use common\models\User;
use common\models\Users;
use common\modules\selectdb\Module;
use yii\base\Exception;
use yii\db\Connection;
use \yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Toir control panel class
 *
 * @category Category
 * @package  Api\controllers
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class ControlPanelController extends Controller
{
    /**
     * Стартовый метод по умолчанию.
     *
     * @return string Содержимое ответа.
     */
    //public function actionIndex()
    //{
        // return $this->render('index');
    //}

    /**
     * Создаём сервис (суть новую базу данных, накатываем миграции со схемой базы
     * и создаём первого пользователя в ней)
     *
     * @return array В массиве элемент pid имеет значение id процесса по которому
     * в методе @actionCheckStatusMigrate проверяем текущее состояние процесса.
     * @throws \yii\web\HttpException
     */
    public function actionCreateService()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        // TODO: принимать запрос только через POST
        $sid = self::_getSid();
        $receivedHash = self::_getHash();
        if ($receivedHash == null) {
            throw new HttpException(500, 'Не верный хеш.');
        }

        // достаём секрет для хеширования
        $secret = \Yii::$app->params['apisecret'];
        // считаем для проверки хеш переданных данных
        $localHash = md5($sid . $secret);
        if ($receivedHash != $localHash) {
            throw new HttpException(500, 'Не верный хеш.');
        }

        $dbServer = \Yii::$app->params['dbserver'];
        $connection = new Connection(
            [
                'dsn' => 'mysql:host=' . $dbServer['host'],
                'username' => $dbServer['username'],
                'password' => $dbServer['password'],
                'charset' => 'utf8',]
        );
        $connection->open();

        $dbName = 'db' . $sid;

        // проверяем существование базы
        try {
            $dbExists = false;
            $connection->createCommand('USE ' . $dbName)->execute();
            $dbExists = true;
        } catch (\Exception $e) {
        } finally {
            if ($dbExists) {
                // TODO: возможно здесь нужно более детальное сообщение
                // например что база(сервис) в процессе создания
                // создание сервиса было не успешным и т.п.
                throw new HttpException(500, 'База данных уже существует.');
            }
        }

        // создаём базу
        $rc = $connection->createCommand('CREATE DATABASE ' . $dbName)->execute();
        if ($rc !== 1) {
            throw new HttpException(500, 'База данных не создана!');
        }

        $connection->close();
        $connection->dsn = 'mysql:host=' . $dbServer['host'] . ';dbname=' . $dbName;
        $connection->open();
        \Yii::$app->set('db', $connection);

        // создаём для неё конфигурационный файл
        $replacements = [
            '%dbname%' => $dbName,
            '%host%' => $dbServer['host'],
            '%username%' => $dbServer['username'],
            '%password%' => $dbServer['password'],
        ];
        $template = "<?php
return [
    '%dbname%' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=%host%;dbname=%dbname%',
        'username' => '%username%',
        'password' => '%password%',
        'charset' => 'utf8',
    ],
];";

        $config = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
        $configDir = \Yii::getAlias('@common/config/conf.d/');
        file_put_contents($configDir . $dbName . '.php', $config);

        // накатываем схему базы
        return self::_doMigrate($dbName);
    }

    /**
     * Проверяем состояние процесса накатывания миграций на новую базу.
     *
     * @return array В массиве элемент status имеет значение process в случае
     * выполнения процесса и complete в случае успешного применения миграций.
     *
     * @throws \yii\web\HttpException
     */
    public function actionCheckStatusMigrate()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $sid = self::_getSid();
        $pid = self::_getPid();
        $receivedHash = self::_getHash();
        if ($receivedHash == null) {
            throw new HttpException(500, 'Не верный хеш.');
        }

        // достаём секрет для хеширования
        $secret = \Yii::$app->params['apisecret'];
        // считаем для проверки хеш переданных данных
        $localHash = md5($sid . $pid . $secret);
        if ($receivedHash != $localHash) {
            throw new HttpException(500, 'Не верный хеш.');
        }

        // проверяем наличие запущенного процесса
        if (self::_isRunning($pid)) {
            return ['status' => 'process'];
        }

        // процесса нет, проверяем наличие lock файла
        $apilog = \Yii::getAlias('@api/runtime/logs');
        $filename = $apilog . '/db' . $sid . '.lock';
        if (!file_exists($filename)) {
            throw new HttpException(500, 'Не существующий сервис!');
        }

        $rc = file_get_contents($filename);

        if (intval($rc) === 0) {
            $rc = self::_doAfterMigrate($sid, 'db' . $sid);
            $rc['status'] = 'complete';
            // удаляем файл по которому мы определяем статус
            // завершения создания сервиса
            unlink($filename);
            return $rc;
        } else {
            throw new HttpException(500, 'Миграция базы данных не выполненна!');
        }
    }

    /**
     * Запускаем процесс применения миграций при создании нового сервиса.
     *
     * @param string $dbname Имя создаваемой базы данных.
     *
     * @return array В массиве элемент pid имеет значение id процесса по которому
     * в методе @actionCheckStatusMigrate проверяем текущее состояние процесса.
     */
    private static function _doMigrate($dbname)
    {
        $cwd = dirname(\Yii::getAlias('@common'));
        $apilogs = \Yii::getAlias('@api/runtime/logs');
        $cmd = sprintf(
            '%s %s %s > %s 2>&1 & echo $!',
            $cwd . '/updb.sh',
            $dbname,
            $cwd,
            $apilogs . '/' . $dbname . '.log'
        );
        $pid = shell_exec($cmd);

        return ['pid' => $pid];
    }

    /**
     * Выполняем необходимые действия после создания базы и применения миграций.
     * Создание пользователя. Установка соответсвия пользователя базе и т.д.
     *
     * @param integer $sid    Ид сервиса.
     * @param string  $dbName Имя базы данных.
     *
     * @return array Массив с именем и паролем пользователя(username, password).
     * @throws \yii\web\HttpException
     */
    private static function _doAfterMigrate($sid, $dbName)
    {
        $dbServer = \Yii::$app->params['dbserver'];
        $connection = new Connection(
            ['dsn' => 'mysql:host=' . $dbServer['host'],
            'username' => $dbServer['username'],
            'password' => $dbServer['password'],
            'charset' => 'utf8', ]
        );
        $connection->dsn = 'mysql:host=' . $dbServer['host'] . ';dbname=' . $dbName;
        $connection->open();
        \Yii::$app->set('db', $connection);

        // генерируем пароль для админа
        $adminPassword = self::_newPassword();
        // создаём пользователя админа
        $adminUser = new User();
        $adminUser->username = 'admin' . $sid;
        $adminUser->password = $adminPassword;
        $adminUser->auth_key = \Yii::$app->getSecurity()->generateRandomString();
        if (!$adminUser->save()) {
            throw new HttpException(
                500, 'Не удалось сохранить пользователя!'
            );
        }

        // сохраняем изменнённое имя пользователя
        $adminUser->username = $adminUser->username . $adminUser->id;
        if (!$adminUser->save()) {
            throw new HttpException(
                500, 'Не удалось сохранить пользователя повторно!'
            );
        }

        // создаём пользователя админа в операторах
        $currentTime = date('Y-m-d\TH:i:s');

        $adminOperator = new Users();
        $adminOperator->uuid = (new MainFunctions)->GUID();
        $adminOperator->name = 'Default User';
        $adminOperator->login = $adminUser->username;
        $adminOperator->pass = $adminPassword;
        $adminOperator->type = 2;
        $adminOperator->tagId = '123456789';
        $adminOperator->active = 1;
        $adminOperator->whoIs = 'Не определен';
        $adminOperator->image = '';
        $adminOperator->contact = 'Не указан';
        $adminOperator->userId = 1;
        $adminOperator->createdAt=$currentTime;
        $adminOperator->changedAt=$currentTime;
        $adminOperator->connectionDate=$currentTime;

        if (!$adminOperator->save()) {
            throw new HttpException(
                500, 'Не удалось сохранить оператора!'
            );
        }

        // TODO: рассылаем файл конфигурации по всем копиям приложения

        $redis = \Yii::$app->redis;
        // добавляем в redis соответствие пользователя базе
        $redis->set($adminUser->username, $dbName);
        // добавляем в redis соответствие сервиса базе
        $redis->set('serviceId' . $sid, $dbName);

        // создаём папку для хранения статики клиента
        $staticFolder = \Yii::getAlias('@backend/web/storage/') . $dbName;
        if (!mkdir($staticFolder)) {
            throw new HttpException(
                500, 'Не удалось создать папку для хранения статики.'
            );
        }

        // возвращаем в панель управления сгенерированного пользователя
        return [
            'username' => $adminUser->username,
            'password' => $adminPassword,
        ];
    }

    /**
     * Меняет пароль администратора сервиса.
     *
     * @return array Массив с именем и паролем пользователя(username, password).
     * @throws \yii\web\HttpException
     */
    public function actionChangeAdminPassword()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        // TODO: принимать запрос только через POST
        $sid = self::_getSid();
        $receivedHash = self::_getHash();
        if ($receivedHash == null) {
            throw new HttpException(500, 'Не верный хеш.');
        }

        // достаём секрет для хеширования
        $secret = \Yii::$app->params['apisecret'];
        // считаем для проверки хеш переданных данных
        $localHash = md5($sid . $secret);
        if ($receivedHash != $localHash) {
            throw new HttpException(500, 'Не верный хеш.');
        }

        $db = Module::chooseByServiceId($sid);
        \Yii::$app->set('db', \Yii::$app->$db);

        // находим пользователя admin
        $users = User::find()->where(['username', 'admin' . $sid])->all();

        if (count($users) == 0) {
            throw new HttpException(404, 'Не найден пользователь!');
        }

        if (count($users) > 1) {
            throw new HttpException(404, 'Пользователей больше одного!');
        }

        $user = $users[0];
        $newpassword = self::_newPassword();
        $user->password = $newpassword;
        $user->update();
        // возвращаем в панель управления сгенерированного пользователя
        return [
            'username' => $user->username,
            'password' => $newpassword,
        ];
    }

    /**
     * Выдаёт список пользователей для указанного сервиса.
     *
     * @return array
     * @throws \yii\web\HttpException
     */
    public function actionUserList()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $sid = self::_getSid();
        $receivedHash = self::_getHash();
        if ($receivedHash == null) {
            throw new HttpException(500, 'Не верный хеш.');
        }

        // достаём секрет для хеширования
        $secret = \Yii::$app->params['apisecret'];
        // считаем для проверки хеш переданных данных
        $localHash = md5($sid . $secret);
        if ($receivedHash != $localHash) {
            throw new HttpException(500, 'Не верный хеш.');
        }

        $db = Module::chooseByServiceId($sid);
        \Yii::$app->set('db', \Yii::$app->$db);

        $users = User::find()->all();
        $result = array();
        foreach ($users as $user) {
            $result[$user->id] = [
                'id' => $user->id,
                'username' => $user->username,
                'status' => $user->status,
            ];
        }

        return $result;
    }

    /**
     * Создаёт случайную строку символов в качестве пароля.
     *
     * @param integer $length Длина пароля.
     *
     * @return string Пароль.
     */
    private static function _newPassword($length = 6)
    {
        $alpha = "abcdefghijklmnopqrstuvwxyz";
        $alpha_upper = strtoupper($alpha);
        $numeric = "0123456789";
        $special = ".-+=_,!@$#*%<>[]{}";
        $chars = "";

        $chars .= $alpha;
        $chars .= $alpha_upper;
        $chars .= $numeric;
        $chars .= $special;

        $len = strlen($chars);
        $pw = '';

        for ($i = 0; $i < $length; $i++) {
            $pw .= substr($chars, rand(0, $len - 1), 1);
        }

        return str_shuffle($pw);
    }

    /**
     * Возвращает ид сервиса из запроса.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    private static function _getSid()
    {
        $request = \Yii::$app->request;
        $sid = $request->get('sid', null);
        if ($sid == null) {
            throw new HttpException(500, 'Не указан id сервиса!');
        }

        return $sid;
    }

    /**
     * Возвращает ид процесса применения миграций к базе данных из запроса.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    private static function _getPid()
    {
        $request = \Yii::$app->request;
        $pid = $request->get('pid', null);
        if ($pid == null) {
            throw new HttpException(500, 'Не указан pid процесса!');
        }

        return $pid;
    }

    /**
     * Получаем из запроса хеш передаваемых данных.
     *
     * @return string|null
     */
    private static function _getHash()
    {
        $request = \Yii::$app->request;
        $hash = $request->get('hash', null);
        return $hash;
    }

    /**
     * Проверяет состояние процесса применения миграций к базе данных.
     *
     * @param integer $pid Идентификатор процесса.
     *
     * @return boolean
     */
    private static function _isRunning($pid)
    {
        try {
            $result = shell_exec(sprintf('ps %d', $pid));
            if (count(preg_split("/\n/", $result)) > 2) {
                return true;
            }
        } catch (Exception $e) {
        }

        return false;
    }
}
