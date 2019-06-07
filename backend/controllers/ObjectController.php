<?php

namespace backend\controllers;

use backend\models\ObjectsSearch;
use common\components\MainFunctions;
use common\models\Contragent;
use common\models\House;
use common\models\ObjectContragent;
use common\models\Objects;
use common\models\Street;
use common\models\UserHouse;
use common\models\Users;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * ObjectController implements the CRUD actions for Object model.
 */
class ObjectController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @throws UnauthorizedHttpException
     */
    public function init()
    {

        if (Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    /**
     * Lists all Object models.
     * @return mixed
     */
    public function actionIndex()
    {
        return self::actionTable();
    }

    /**
     * Lists all Object models.
     * @return mixed
     */
    public function actionTable()
    {
        $searchModel = new ObjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 1200;

        return $this->render('table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Object model.
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
     * Creates a new Flat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Objects();
        $searchModel = new ObjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new ObjectsSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination->pageSize = 15;
            //if ($_GET['from'])
            MainFunctions::register('object', 'Добавлен объект',
                '<a class="btn btn-default btn-xs">' . $model['objectType']['title'] . '</a> ' . $model['title'] . '<br/>' .
                '<a class="btn btn-default btn-xs">Адрес</a> ул.' . $model['house']['street']['title'] . ',д.' . $model['house']['number']);

            return $this->render('table', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model, 'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Updates an existing Object model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
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
     * Deletes an existing Object model.
     * If deletion is successful, the browser will be redirected to the 'table' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['table']);
    }

    /**
     * Finds the Object model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Objects
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Objects::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Build tree of equipment by user
     *
     * @return mixed
     */
    public function actionTree()
    {
        ini_set('memory_limit', '-1');
        $fullTree = array();
        $streets = Street::find()
            ->select('*')
            ->orderBy('title')
            ->all();
        foreach ($streets as $street) {
            $fullTree['children'][] = [
                'title' => $street['title'],
                'address' => $street['city']['title'] . ', ул.' . $street['title'],
                'type' => 'street',
                'expanded' => true,
                'uuid' => $street['uuid'],
                'folder' => true
            ];
            $houses = House::find()->select('uuid, number')->where(['streetUuid' => $street['uuid']])->
            orderBy('number')->all();
            foreach ($houses as $house) {
                $user_house = UserHouse::find()->select('_id')->where(['houseUuid' => $house['uuid']])->one();
                $user = Users::find()->where(['uuid' =>
                    UserHouse::find()->where(['houseUuid' => $house['uuid']])->one()
                ])->one();
                $childIdx = count($fullTree['children']) - 1;
                $fullTree['children'][$childIdx]['children'][] = [
                    'title' => $house['number'],
                    'address' => $street['title'] . ', ' . $house['number'],
                    'type' => 'house',
                    'expanded' => true,
                    'uuid' => $house['uuid'],
                    'folder' => true
                ];
                $objects = Objects::find()->where(['houseUuid' => $house['uuid']])->all();
                foreach ($objects as $object) {
                    if (!$object['deleted']) {
                        $childIdx2 = count($fullTree['children'][$childIdx]['children']) - 1;
                        $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][] = [
                            'title' => $object['objectType']['title'] . ' ' . $object['title'],
                            'address' => $street['title'] . ', ' . $object['house']['number'] . ', ' . $object['title'],
                            'type' => 'object',
                            'uuid' => $object['uuid'],
                            'folder' => true
                        ];
                        $objectContragents = ObjectContragent::find()->where(['objectUuid' => $object['uuid']])->all();
                        foreach ($objectContragents as $objectContragent) {
                            if (!$objectContragent['contragent']['deleted']) {
                                $childIdx3 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                                $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] = [
                                    'title' => $objectContragent['contragent']['title'],
                                    'address' => $objectContragent['contragent']['address'],
                                    'inn' => $objectContragent['contragent']['inn'],
                                    'phone' => $objectContragent['contragent']['phone'],
                                    'email' => $objectContragent['contragent']['email'],
                                    'contragentType' => $objectContragent['contragent']['contragentType']['title'],
                                    'date' => $objectContragent['contragent']['createdAt'],
                                    'type' => 'contragent',
                                    'uuid' => $objectContragent['contragent']['uuid'],
                                    'folder' => false
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $this->render(
            'tree',
            ['contragents' => $fullTree]
        );
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования или объекта
     *
     * @return mixed
     */
    public
    function actionNew()
    {
        if (isset($_POST["selected_node"])) {
            $folder = $_POST["folder"];
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["type"]))
                $type = $_POST["type"];
            else $type = 0;

            if ($folder == "true" && $uuid && $type) {
                if ($type == 'street') {
                    $house = new House();
                    return $this->renderAjax('_add_house_form', [
                        'streetUuid' => $uuid,
                        'house' => $house
                    ]);
                }
                if ($type == 'house') {
                    $object = new Objects();
                    return $this->renderAjax('_add_object_form', [
                        'houseUuid' => $uuid,
                        'object' => $object
                    ]);
                }
                if ($type == 'object') {
                    $contragent = new Contragent();
                    return $this->renderAjax('_add_contragent_form', [
                        'objectUuid' => $uuid,
                        'contragent' => $contragent
                    ]);
                }
            }
        }
        return 'Нельзя добавить объект в этом месте';
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет редактирование оборудования
     *
     * @return mixed
     */
    public function actionEdit()
    {
        if (isset($_POST["selected_node"])) {
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["type"]))
                $type = $_POST["type"];
            else $type = 0;

            if ($uuid && $type) {
                if ($type == 'street') {
                    $street = Street::find()->where(['uuid' => $uuid])->one();
                    if ($street) {
                        return $this->renderAjax('_add_street_form', [
                            'street' => $street,
                            'streetUuid' => $uuid
                        ]);
                    }
                }
                if ($type == 'house') {
                    $house = House::find()->where(['uuid' => $uuid])->one();
                    if ($house) {
                        return $this->renderAjax('_add_house_form', [
                            'houseUuid' => $uuid,
                            'house' => $house
                        ]);
                    }
                }

                if ($type == 'object') {
                    $object = Objects::find()->where(['uuid' => $uuid])->one();
                    if ($object) {
                        return $this->renderAjax('_add_object_form', [
                            'objectUuid' => $uuid,
                            'object' => $object
                        ]);
                    }
                }
                if ($type == 'contragent') {
                    $contragent = Contragent::find()->where(['uuid' => $uuid])->one();
                    return $this->renderAjax('_add_contragent_form', [
                        'contragentUuid' => $uuid,
                        'contragent' => $contragent
                    ]);
                }
            }
        }
        return 'Нельзя отредактировать этот объект';
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет удаление
     *
     * @return mixed
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionRemove()
    {
        if (isset($_POST["selected_node"])) {
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["type"]))
                $type = $_POST["type"];
            else $type = 0;

            if ($uuid && $type) {
                if ($type == 'street') {
                    $street = Street::find()->where(['uuid' => $uuid])->one();
                    if ($street) {
                        $house = House::find()->where(['streetUuid' => $street['uuid']])->one();
                        if (!$house) {
                            $street->delete();
                        }
                    }
                }
                if ($type == 'house') {
                    $house = House::find()->where(['uuid' => $uuid])->one();
                    if ($house) {
                        $object = Objects::find()->where(['houseUuid' => $house['uuid']])->one();
                        if (!$object) {
                            $house->delete();
                        }
                    }
                }
                if ($type == 'object') {
                    $object = Objects::find()->where(['uuid' => $uuid])->one();
                    if ($object) {
                        $object['deleted'] = true;
                        $object->save();
                    }

                }

                if ($type == 'contragent') {
                    $contragent = Contragent::find()->where(['uuid' => $uuid])->one();
                    if ($contragent) {
                        $contragent['deleted'] = true;
                        $contragent->save();
                    }

                }
            }
        }
        return 'Нельзя удалить этот объект';
    }

    /**
     * Creates a new Object model.
     * @return mixed
     */
    public
    function actionSave()
    {
        if (isset($_POST["type"]))
            $type = $_POST["type"];
        else $type = 0;

        if ($type) {
            if ($type == 'street') {
                if (isset($_POST['streetUuid'])) {
                    $model = Street::find()->where(['uuid' => $_POST['streetUuid']])->one();
                    if ($model->load(Yii::$app->request->post())) {
                        if ($model->save(false)) {
                            return $this->redirect(['/object/tree']);
                        }
                    }
                }
            }
            if ($type == 'house') {
                if (isset($_POST['houseUuid']))
                    $model = House::find()->where(['uuid' => $_POST['houseUuid']])->one();
                else
                    $model = new House();
                if ($model->load(Yii::$app->request->post())) {
                    if ($model->save(false)) {
                        return $this->redirect(['/object/tree']);
                    }
                }
            }
            if ($type == 'object') {
                if (isset($_POST['objectUuid']))
                    $model = Objects::find()->where(['uuid' => $_POST['objectUuid']])->one();
                else
                    $model = new Objects();
                if ($model->load(Yii::$app->request->post())) {
                    if ($model->save(false)) {
                        return $this->redirect(['/object/tree']);
                    }
                }
            }
            if ($type == 'contragent') {
                if (isset($_POST['contragentUuid']))
                    $model = Contragent::find()->where(['uuid' => $_POST['contragentUuid']])->one();
                else
                    $model = new Contragent();
                if ($model->load(Yii::$app->request->post())) {
                    if ($model->save(false) && isset($_POST['objectUuid'])) {
                        $objectContragent = new ObjectContragent();
                        $objectContragent->contragentUuid = $model['uuid'];
                        $objectContragent->uuid = MainFunctions::GUID();
                        $objectContragent->oid = Users::ORGANISATION_UUID;
                        $objectContragent->objectUuid = $_POST['objectUuid'];
                        $objectContragent->save();
                        return $this->redirect(['/object/tree']);
                    }
                }
            }
        }
        return $this->redirect(['/object/tree']);
    }
}
