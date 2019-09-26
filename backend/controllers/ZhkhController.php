<?php


namespace backend\controllers;


use common\components\ZhkhActiveRecord;
use common\models\IPermission;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 *
 * @property array $actionPermissions
 * @property array $permissions
 */
class ZhkhController extends Controller
{
    protected $modelClass;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            /** @var IPermission $model */
            $model = new $this->modelClass;
            if (!($model instanceof ZhkhActiveRecord)) {
                // если это "общая" для всех модель, значит доступа нет ни у кого.
                Yii::$app->session->setFlash('error', '<h3>Не достаточно прав доступа.</h3>');
                $this->redirect('/');
                return false;
            }

            $currentUser = Yii::$app->user;
            // Если пользователь администратор вообще больше ни чего не проверяем
            if ($currentUser->can(User::ROLE_ADMIN)) {
                return true;
            }

            $access = false;
            $actPermissions = $model->getActionPermissions();
            $act = $this->action->id;
            foreach ($actPermissions as $permission => $actions) {
                if (in_array($act, $actions)) {
                    $tmpArray = explode('\\', $this->modelClass);
                    $reqPermission = $permission . end($tmpArray);
                    if ($currentUser->can($reqPermission)) {
                        $access = true;
                    }
                }
            }

            if (!$access) {
                Yii::$app->session->setFlash('error', '<h3>Не достаточно прав доступа.</h3>');
                $this->redirect(['/']);
            }

            return $access;
        }

        return false;
    }
}