<?php

namespace backend\controllers;

use backend\models\ContragentSearch;
use backend\models\SignupForm;
use backend\models\UserArm;
use common\components\MainFunctions;
use common\components\Tag;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\ObjectContragent;
use common\models\User;
use common\models\UserContragent;
use common\models\Users;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * ContragentController implements the CRUD actions for Contragent model.
 */
class ContragentController extends ZhkhController
{
    protected $modelClass = Contragent::class;

    /**
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionIndex()
    {
        return self::actionTable();
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionTable()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Contragent::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['Contragent'][$_POST['editableIndex']]['title'];
            }
            if ($_POST['editableAttribute'] == 'inn') {
                $model['inn'] = $_POST['Contragent'][$_POST['editableIndex']]['inn'];
            }
            if ($_POST['editableAttribute'] == 'contragentTypeUuid') {
                $model['contragentTypeUuid'] = $_POST['Contragent'][$_POST['editableIndex']]['contragentTypeUuid'];
            }
            if ($_POST['editableAttribute'] == 'phone') {
                $model['phone'] = $_POST['Contragent'][$_POST['editableIndex']]['phone'];
            }
            if ($_POST['editableAttribute'] == 'director') {
                $model['director'] = $_POST['Contragent'][$_POST['editableIndex']]['director'];
            }
            if ($_POST['editableAttribute'] == 'address') {
                $model['address'] = $_POST['Contragent'][$_POST['editableIndex']]['address'];
            }
            if ($_POST['editableAttribute'] == 'account') {
                $model['account'] = $_POST['Contragent'][$_POST['editableIndex']]['account'];
            }
            if ($_POST['editableAttribute'] == 'email') {
                $model['email'] = $_POST['Contragent'][$_POST['editableIndex']]['email'];
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new ContragentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        if (isset($_GET['objectUuid'])) {
            $objectContragents = ObjectContragent::find()
                ->where(['objectUuid' => $_GET['objectUuid']])
                ->all();
            $contragents = [];
            foreach ($objectContragents as $objectContragent) {
                $contragents[] = $objectContragent['contragentUuid'];
            }
            $dataProvider->query->andWhere(['IN', 'uuid', $contragents]);
        }
        if (Yii::$app->request->isAjax && isset($_POST['objectUuid'])) {
            return $this->redirect('../contragent/index?objectUuid=' . $_POST['objectUuid']);
        }

        return $this->render('table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Contragent model.
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
     * Creates a new Contragent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws Exception
     * @throws Throwable
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can(User::ROLE_ADMIN) &&
            !Yii::$app->user->can(User::ROLE_OPERATOR)) {
            $this->redirect('index');
        }

        $contragent = new Contragent();
        $searchModel = new ContragentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        if ($contragent->load(Yii::$app->request->post())) {
            $contragent->title = htmlspecialchars($contragent->title);
            if ($contragent->save()) {
                if (isset ($_POST['objectUuid'])) {
                    $objectContragent = new ObjectContragent();
                    $objectContragent->contragentUuid = $contragent['uuid'];
                    $objectContragent->uuid = MainFunctions::GUID();
                    $objectContragent->oid = Users::getCurrentOid();
                    $objectContragent->objectUuid = $_POST['objectUuid'];
                    $objectContragent->save();
                }
                $contractorTypes = [ContragentType::CONTRACTOR];
                if (in_array($contragent->contragentTypeUuid, $contractorTypes)) {
                    $model = new UserArm();
                    $am = Yii::$app->getAuthManager();
                    $existUser = User::find()->all();
                    $login = 'user' . (count($existUser) + 1);
                    if (!$contragent->email)
                        $model->email = $login . '@' . time() . '.ru';
                    else
                        $model->email = $contragent->email;
                    $model->username = $model->email;

                    $model->type = Users::USERS_WORKER;
                    $model->password = SignupForm::randomString();
                    $model->tagType = Tag::TAG_TYPE_UHF;
                    $model->pin = '1234';
                    $model->name = $contragent->title;
                    // очевидно из за условия выше данная проверка линяя
                    if ($contragent->contragentTypeUuid == ContragentType::CONTRACTOR) {
                        $model->whoIs = 'Подрядная огранизация';
                    }

                    $model->contact = $contragent->phone;
                    $model->role = User::ROLE_OPERATOR;
                    $model->status = User::STATUS_ACTIVE;

                    if ($model->validate()) {
                        $user = new User();
                        $user->username = $model->username;
                        $user->auth_key = Yii::$app->security->generateRandomString();
                        $user->password_hash = Yii::$app->security->generatePasswordHash($model->password);
                        $user->email = $model->email;
                        if ($user->save()) {
                            $users = new Users();
                            $users->uuid = MainFunctions::GUID();
                            $users->name = htmlspecialchars($model->name);
                            $users->type = $model->type;
                            $users->pin = $users->type == Users::USERS_WORKER ? $model->tagType . ':' . $model->pin : '-';
                            $users->active = 1;
                            $users->whoIs = $model->whoIs;
                            $users->contact = $model->contact;
                            $users->user_id = $user->id;
                            $users->image = '';
                            $users->oid = Users::getCurrentOid();
                            if ($users->validate() && $users->save()) {
                                $newRole = $am->getRole($model->role);
                                $am->assign($newRole, $users->user_id);
                                MainFunctions::register('user', 'Добавлен пользователь ' . $model->name, $model->contact, $users->uuid);
                                $userContragent = new UserContragent();
                                $userContragent->uuid = MainFunctions::GUID();
                                $userContragent->userUuid = $users->uuid;
                                $userContragent->contragentUuid = $contragent->uuid;
                                $userContragent->oid = Users::getCurrentOid();
                                if (!$userContragent->save())
                                    echo "";
                                //echo "uc ".json_encode($userContragent->errors);
                            } else {
                                //echo "us ".json_encode($users->errors);
                                $user->delete();
                            }
                        } else {
                            //echo "u ".json_encode($user->errors);
                        }
                    }
                }
            }
            return $this->render('table', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('create', [
                'model' => $contragent,
                'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Updates an existing Contragent model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (isset ($_POST['objectUuid'])) {
                // TODO проверка на то что уже привязан
                $objectContragent = new ObjectContragent();
                $objectContragent->contragentUuid = $model['uuid'];
                $objectContragent->uuid = MainFunctions::GUID();
                $objectContragent->oid = Users::getCurrentOid();
                $objectContragent->objectUuid = $_POST['objectUuid'];
                $objectContragent->save();
            }

            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Contragent model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionDelete($id)
    {
        $contragent = $this->findModel($id);
        $contragent['deleted'] = true;
        $contragent->save();

        return $this->redirect(['table']);
    }

    /**
     * Finds the Contragent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contragent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contragent::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionPhone()
    {
        if (isset($_POST['id']))
            if (($model = Contragent::find()->where(['uuid' => $_POST['id']])->one()) !== null) {
                return $model['phone'];
            } else return '';
        return '';
    }

    /**
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionAddress()
    {
        if (isset($_POST['id']))
            if (($model = Contragent::find()->where(['uuid' => $_POST['id']])->one()) !== null) {
                $object = ObjectContragent::find()->where(['contragentUuid' => $model['uuid']])->one();
                $address = [];
                if ($object) {
                    $address['city'] = $object['object']['house']['street']['cityUuid'];
                    $address['street'] = $object['object']['house']['streetUuid'];
                    $address['house'] = $object['object']['houseUuid'];
                    $address['object'] = $object['objectUuid'];
                    return json_encode($address);
                }
            }
        return json_encode([]);
    }

    /**
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionName()
    {
        if (isset($_POST['id']))
            if (($model = Contragent::find()->where(['uuid' => $_POST['id']])->one()) !== null) {
                return $model['title'];
            } else return '';
        return '';
    }

    /**
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionForm()
    {
        if (isset($_GET["uuid"])) {
            $model = Contragent::find()->where(['uuid' => $_GET["uuid"]])->one();
        } else {
            $model = new Contragent();
        }
        return $this->renderAjax('_add_contragent', ['model' => $model]);
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionList()
    {
        //if (isset($_POST["uuid"]))
        //$contragents = Contragent::find()->orderBy("title")->all();
        $contragents = Contragent::find()
            ->where(['contragentTypeUuid' => [ContragentType::ORGANIZATION, ContragentType::CITIZEN]])
            ->andWhere(['deleted' => false])
            ->orderBy('title DESC')
            ->all();
        $items = ArrayHelper::map($contragents, 'uuid', 'title');
        return json_encode($items);
    }
}
