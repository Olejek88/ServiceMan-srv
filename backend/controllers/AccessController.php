<?php

namespace backend\controllers;

use backend\models\AccessModel;
use backend\models\AccessSearch;
use common\models\User;
use common\models\Users;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\rbac\Item;
use yii\rbac\ManagerInterface;

class AccessController extends ZhkhController
{
    protected $modelClass = AccessModel::class;

    /**
     * @return array
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
        ];
    }

    /**
     * @return string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionUpdate()
    {
        $am = Yii::$app->authManager;
        $this->updateAccess($am, Yii::$app->request->post('admin', []), $am->getRole(User::ROLE_ADMIN));
        $this->updateAccess($am, Yii::$app->request->post('oper', []), $am->getRole(User::ROLE_OPERATOR));
        $this->updateAccess($am, Yii::$app->request->post('disp', []), $am->getRole(User::ROLE_DISPATCH));
        $this->updateAccess($am, Yii::$app->request->post('dir', []), $am->getRole(User::ROLE_DIRECTOR));
        return $this->actionIndex();
    }

    /**
     * @param $am ManagerInterface
     * @param $items array
     * @param $role Item
     * @throws Exception
     */
    private function updateAccess($am, $items, $role)
    {
        foreach ($items as $pName => $values) {
            if ($values['ch'] == 1) {
                $pObj = $am->getPermission($pName . '-' . Users::getCurrentOid());
                if ($values['value'] == 1) {
                    $am->addChild($role, $pObj);
                } else {
                    $am->removeChild($role, $pObj);
                }
            }
        }
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $searchModel = new AccessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 100;
        if (Yii::$app->request->getHeaders()->has('X-PJAX')) {
            $data = $this->renderAjax('index',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]
            );
        } else {
            $data = $this->render('index',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]
            );
        }

        return $data;
    }
}
