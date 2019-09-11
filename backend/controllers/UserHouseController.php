<?php

namespace backend\controllers;

use app\commands\MainFunctions;
use backend\models\UserHouseSearch;
use common\models\EquipmentSystem;
use common\models\House;
use common\models\Street;
use common\models\UserHouse;
use common\models\Users;
use common\models\UserSystem;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/**
 * UserHouseController implements the CRUD actions for UserHouse model.
 */
class UserHouseController extends ZhkhController
{
    /**
     * Lists all House models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserHouseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserHouse model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserHouse model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        parent::actionCreate();

        $model = new UserHouse();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new UserHouseSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination->pageSize = 100;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a records for all House model.
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionCreateDefault()
    {
        $houses = House::find()
            ->all();
        $currentUser = Users::find()
            ->where('user_id>3')
            ->asArray()
            ->one();

        foreach ($houses as $house) {
            $userHouse = UserHouse::find()
                ->where(['houseUuid' => $house['uuid']])
                ->all();
            if ($userHouse == null) {
                $model = new UserHouse();
                $model->uuid = MainFunctions::GUID();
                $model->userUuid = $currentUser['uuid'];
                $model->houseUuid = $house['uuid'];
                $model->changedAt = date('Y-m-d H:i:s');
                $model->createdAt = date('Y-m-d H:i:s');
                echo('store user house: ' . $model->uuid . ' [' . $model->userUuid . ']' . PHP_EOL . '<br/>');
                $model->save();
            }
        }
        $searchModel = new UserHouseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing UserHouse model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        parent::actionUpdate($id);

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserHouse model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        parent::actionDelete($id);

        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the UserHouse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserHouse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserHouse::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Build tree of equipment by user
     *
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionTree()
    {
        ini_set('memory_limit', '-1');
        $fullTree = array();
        $streets = Street::find()
            ->select('*')
            ->andWhere(['deleted' => false])
            ->orderBy('title')
            ->all();
        foreach ($streets as $street) {
            $fullTree['children'][] = [
                'title' => 'ул.' . $street['title'],
                'address' => $street['city']['title'] . ', ул.' . $street['title'],
                'uuid' => $street['uuid'],
                'type' => 'street',
                'key' => $street['_id'],
                'folder' => true,
                'expanded' => true
            ];
            $childIdx = count($fullTree['children']) - 1;
            $childIdx2 = 0;
            $houses = House::find()->select('uuid,number')->where(['streetUuid' => $street['uuid']])
                ->andWhere(['deleted' => false])
                ->orderBy('number')->all();
            foreach ($houses as $house) {
                $equipmentSystems = EquipmentSystem::find()->all();
                foreach ($equipmentSystems as $equipmentSystem) {
                    $userSystems = UserSystem::find()->where(['equipmentSystemUuid' => $equipmentSystem['uuid']])->all();
                    $userHouses = UserHouse::find()->where(['houseUuid' => $house['uuid']])->all();
                    $user_list='';
                    $count = 0;
                    foreach ($userSystems as $userSystem) {
                        foreach ($userHouses as $userHouse) {
                            if ($userSystem['userUuid']==$userHouse['userUuid']) {
                                if ($count>0) $user_list.=', ';
                                $user_list.=$userSystem['user']['name'];
                                $count++;
                            }
                        }
                    }
                    if ($user_list!='') {
                        $title = '<span style="background-color: #11aa11; color: white; border-radius: 4px !important; margin: 3px; padding: 3px">'.$user_list.'</span>';
                    } else {
                        $title = '<div class="progress"><div class="critical5">не_назначены</div></div>';
                    }
                    $name = 'system'.$equipmentSystem['_id'];
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['title']=$house['number'];
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['address']=$street['title'] . ', ' . $house['number'];
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['type']='house';
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['expanded']=true;
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['uuid']=$house['uuid'];
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['key']=$house['_id'];
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['folder']=false;
                    $fullTree['children'][$childIdx]['children'][$childIdx2][$name] = Html::a(
                        $title,
                        ['/user-house/change', 'houseUuid' => $house['uuid'],
                            'equipmentSystemUuid' => $equipmentSystem['uuid']],
                        [
                            'title' => 'Сменить пользователей',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalUser',
                        ]);
                }
                $childIdx2++;
            }
        }

        $equipmentSystems = EquipmentSystem::find()->all();

        return $this->render(
            'tree',
            [
                'houses' => $fullTree,
                'systems' => $equipmentSystems
            ]
        );
    }

    /**
     * @return string
     */
    public function actionChange()
    {
        if (isset($_GET["houseUuid"])) {
            $model = new UserHouse();
            if (isset($_GET["equipmentSystemUuid"]))
                return $this->renderAjax('_add_user', ['model' => $model, 'houseUuid' => $_GET["houseUuid"],
                    'equipmentSystemUuid' => $_GET["equipmentSystemUuid"]]);
        }
        return "";
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public
    function actionName()
    {
        if (isset($_POST['userAdd']) && isset($_POST['houseUuid'])) {
            $user = Users::find()->where(['uuid' => $_POST['userAdd']])->one();
            if ($user) {
                $userHouse = UserHouse::find()->where(['houseUuid' => $_POST['houseUuid']])
                    ->andWhere(['userUuid' => $user['uuid']])
                    ->count();
                if ($userHouse==0) {
                    $userHouse = new UserHouse();
                    $userHouse->uuid = \common\components\MainFunctions::GUID();
                    $userHouse->houseUuid = $_POST['houseUuid'];
                    $userHouse->userUuid = $user['uuid'];
                    $userHouse->oid = Users::getCurrentOid();
                    $userHouse->save();
                }
            }
        }

        $users = Users::find()->where(['!=','name','sUser'])->all();
        foreach ($users as $user) {
            $id = 'user-'.$user['_id'];
            if (isset($_POST[$id]) && ($_POST[$id]==1 || $_POST[$id]=="1")) {
                $userHouse = UserHouse::find()->where(['houseUuid' => $_POST['houseUuid']])
                    ->andWhere(['userUuid' => $user['uuid']])
                    ->one();
                if ($userHouse) {
                    $userHouse->delete();
                }
            }
        }
        return true;
    }
}
