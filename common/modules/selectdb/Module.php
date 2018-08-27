<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\modules\selectdb
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\modules\selectdb;

use api\controllers\TokenController;
use yii\console\Application;
use yii\web\HttpException;
use yii\web\Request;

/**
 * Toir module definition class
 *
 * @category Category
 * @package  Common\modules\selectdb
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class Module extends \yii\base\Module
{
    /**
     * Namespace
     *
     * @inheritdoc
     */
    public $controllerNamespace = 'common\modules\selectdb\controllers';

    /**
     * Метод инициализации модуля.
     *
     * @inheritdoc
     *
     * @return void
     *
     * @throws \yii\web\HttpException
     */
    public function init()
    {
        parent::init();

        if (\Yii::$app instanceof Application) {
            return;
        }

        $user = \Yii::$app->user;
        $session = \Yii::$app->getSession();
        $id = $session->getHasSessionId() ||
            $session->getIsActive() ? $session->get($user->idParam) : null;

        if ($id !== null) {
            $db = \Yii::$app->session->get('user.dbname');
            \Yii::$app->set('db', \Yii::$app->$db);
            return;
        }

        $request = \Yii::$app->getRequest();
        // $db = self::_getUserDb($request->getPathInfo(), $request->bodyParams);
        $db = self::_getUserDb($request->getPathInfo(), $request);

        if ($db != null) {
            /* вероятно проверка лишняя, так как если в redis или
            сессии есть соответствие пользователя базе,
            значит база есть и уже готова к использованию */
            $apilog = \Yii::getAlias('@api/runtime/logs');
            // проверяем на то что база еще создаётся
            if (file_exists($apilog . '/' . $db . '.lock')) {
                throw new HttpException(500, 'База данных в процессе создания.');
            }

            \Yii::$app->set('db', \Yii::$app->$db);
        } else {
            // Удаляем куки, для того чтобы предотвратить попытки обращения к базе,
            // в ситуации когда она не выбрана, основываясь на информации в них.
            unset($_COOKIE['_identity-frontend']);
            setcookie('_identity-frontend', null, -1, '/');
            unset($_COOKIE['_identity-backend']);
            setcookie('_identity-backend', null, -1, '/');
        }
        // else {
        //    throw new HttpException(
        //        500, 'Не достаточно данных для выбора базы данных.'
        //    );
        //}
    }

    /**
     * Возвращает имя базы данных для пользователя из запроса.
     *
     * @param string  $pathInfo URI запроса.
     * @param Request $request  Параметры запроса.
     *
     * @return string Имя базы данных. Если не удалось определить - null.
     */
    private static function _getUserDb($pathInfo, $request)
    {
        $bodyParams = $request->bodyParams;
        if ($pathInfo == 'login' || $pathInfo == 'site/login') {
            if (isset($bodyParams['LoginForm'])) {
                return self::_getDbSiteLogin($bodyParams['LoginForm']['username']);
            } else {
                return null;
            }
        } else if ($pathInfo == 'request-password-reset'
            || $pathInfo == 'site/request-password-reset'
        ) {
            if (isset($bodyParams['PasswordResetRequestForm'])) {
                return self::_getDbSitePasswordReset(
                    $bodyParams['PasswordResetRequestForm']['email']
                );
            } else {
                return null;
            }
        } else {
            // проверяем не запрос ли это к api
            if (isset($bodyParams['grant_type'])
                && (isset($bodyParams['label']) || isset($bodyParams['password']))
            ) {
                $tokenType = $bodyParams['grant_type'];
                $password = $bodyParams[$tokenType];

                return self::_getDbSiteLogin($password);
            }

            // проверяем не запрос ли это к api
            if (isset($bodyParams['apiuser'])) {
                return self::_getDbSiteLogin($bodyParams['apiuser']);
            }

            $apiuser = $request->getQueryParam('apiuser');
            if ($apiuser != null) {
                return self::_getDbSiteLogin($apiuser);
            }

            return null;
        }
    }

    /**
     * Возвращает базу данных по имени пользователя из запроса на вход(URI=login).
     *
     * @param string $username Имя пользователя.
     *
     * @return string Имя базы данных, если не удалось определить - null.
     */
    private static function _getDbSiteLogin($username)
    {
        if ($username != '') {
            $db = self::chooseByUserName($username);
            return $db;
        }

        return null;
    }

    /**
     * Возвращает базу данных из запроса на востановление пароля
     * (URI=password-reset).
     *
     * @param string $email Email.
     *
     * @return string Имя базы данных, если не удалось определить - null.
     */
    private static function _getDbSitePasswordReset($email)
    {
        if ($email != '') {
            $db = self::chooseByEmail($email);
            return $db;
        }

        return null;
    }

    /**
     * Метод с логикой выбора базы данных.
     *
     * @param string $email Email.
     *
     * @return string Имя базы данных.
     */
    public static function chooseByEmail($email)
    {
        return self::chooseDb($email, '');
    }

    /**
     * Метод с логикой выбора базы данных.
     *
     * @param string $username Имя пользователя.
     *
     * @return string Имя базы данных.
     */
    public static function chooseByUserName($username)
    {
        // TODO: нужно принудить пользователей входить по email !!!
        // т.к. может получиться ситуация когда в разных базах будут учётки
        // с одним username а в redis будет храниться соответствие username-db
        // для последнего сохранённого пользователя!!!
        // TODO: либо принудительно добавлять номерки к имени при этом контролируя
        // наличие пользователей с номерками в базе.
        return self::chooseDb($username, '');
    }

    /**
     * Метод с логикой выбора базы данных.
     *
     * @param string|int $id     Ид.
     * @param string     $prefix Префикс.
     *
     * @return string Имя базы данных.
     */
    public static function chooseByUserId($id, $prefix = 'userId')
    {
        return self::chooseDb($id, $prefix);
    }

    /**
     * Метод с логикой выбора базы данных.
     *
     * @param string|int $id     Ид.
     * @param string     $prefix Префикс.
     *
     * @return string Имя базы данных.
     */
    public static function chooseByServiceId($id, $prefix = 'serviceId')
    {
        return self::chooseDb($id, $prefix);
    }

    /**
     * Метод с логикой выбора базы данных.
     *
     * @param \yii\web\Request $request Запрос.
     *
     * @return string Имя базы данных.
     */
    public static function chooseByToken($request)
    {
        $token = TokenController::getTokenString($request);
        $user = TokenController::getUserByToken($token);
        return self::chooseByUserName($user->login);
    }

    /**
     * Метод с логикой выбора базы данных.
     *
     * @param string|int $id     Ид.
     * @param string     $prefix Префикс.
     *
     * @return string Имя базы данных.
     * @throws \yii\web\HttpException
     */
    public static function chooseDb($id, $prefix)
    {
        $redis = \Yii::$app->__get('redis');
        $result = $redis->get($prefix . $id);
        if ($result != null) {
            return $result;
        } else {
            throw new HttpException(500, 'Db not choosen.');
        }
    }
}
