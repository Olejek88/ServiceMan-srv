<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace backend\controllers;

use backend\models\ResidentSearch;
use common\models\City;
use common\models\Flat;
use common\models\House;
use common\models\Measure;
use common\models\Resident;
use common\models\Street;
use common\models\Subject;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;

use common\models\Objects;
use common\models\Equipment;
use common\models\ObjectType;

use backend\models\ObjectsSearch;

/**
 * ResidentsController implements the CRUD actions for Residents model.
 */
class ResidentsController extends Controller
{
    /**
     * Behaviors
     *
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Init
     *
     * @return void
     * @throws UnauthorizedHttpException
     */
    public function init()
    {
        if (\Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }
    }

    /**
     * Lists all Residents models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Resident::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'owner') {
                $model['owner'] = $_POST['Resident'][$_POST['editableIndex']]['owner'];
            }
            if ($_POST['editableAttribute'] == 'inn') {
                $model['inn'] = $_POST['Resident'][$_POST['editableIndex']]['inn'];
            }
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }

        $searchModel = new ResidentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        return $this->render('table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Residents models.
     *
     * @return mixed
     */
    public function actionTable()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Resident::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'owner') {
                $model['owner'] = $_POST['Resident'][$_POST['editableIndex']]['owner'];
            }
            if ($_POST['editableAttribute'] == 'inn') {
                $model['inn'] = $_POST['Resident'][$_POST['editableIndex']]['inn'];
            }
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }

        $searchModel = new ResidentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        return $this->render('table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Action list
     *
     * @return mixed
     * @throws UnauthorizedHttpException
     */
    public function actionList()
    {
        $listResidents = Resident::find()
            ->asArray()
            ->all();

        return $this->render(
            'list',
            [
                'model' => $listResidents
            ]
        );
    }

    /**
     * Displays a single Resident model.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new Resident model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Resident();
        $searchModel = new ResidentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        if ($model->load(Yii::$app->request->post())) {
            // проверяем все поля, если что-то не так показываем форму с ошибками
            if (!$model->validate()) {
                echo var_dump($model);
                echo var_dump($model->errors);
                return $this->render('create', ['model' => $model, 'dataProvider' => $dataProvider]);
            }

            // сохраняем запись
            if ($model->save(false)) {
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render('create', ['model' => $model, 'dataProvider' => $dataProvider]);
            }
        } else {
            return $this->render('create', ['model' => $model, 'dataProvider' => $dataProvider]);
        }
    }

    /**
     * Вспомогательный метод для постройки дерева.
     *
     * @param Resident   $resident    Абонент
     * @param Equipment $equipment Оборудование
     *
     * @return mixed
     */
    function setObjectParameters($resident, $equipment)
    {
        $resident['title'] = Html::a(
            $equipment['title'],
            ['equipment/view', 'id' => $equipment['_id']]
        );
        $resident['inventory'] = $equipment['inventoryNumber'];
        $resident['serial'] = $equipment['serialNumber'];
        $resident['date'] = $equipment['startDate'];
        $resident['tag'] = $equipment['tagId'];
        $resident['type'] = $equipment['equipmentModel']->title;
        $sTitle = $equipment['equipmentStatus']->title;

        if ($sTitle == 'Требует ремонта' || $sTitle == 'Неисправно') {
            $class = 'critical1';
        } elseif ($sTitle == 'Не установлено' || $sTitle == 'Требует проверки') {
            $class = 'critical2';
        } else {
            $class = 'critical3';
        }

        $resident['status'] = '<div class="progress"><div class="'
            . $class . '">' . $sTitle . '</div></div>';

        return $resident;
    }

    /**
     * Метод для постройки дерева в три уровня.
     *
     * @return string
     */
    public function actionTree()
    {

        $c = 'children';
        $fullTree = array();
        $streets = Street::find()
            ->select('*')
            ->orderBy('title')
            ->all();
        $oCnt0 = 0;
        foreach ($streets as $street) {
            $fullTree[$oCnt0]['title'] = Html::a(
                $street['title'],
                ['street/view', '_id' => $street['_id']]
            );
            $fullTree[$oCnt0]['type'] = 'Улица';
            $fullTree[$oCnt0]['street'] = $street['title'];
            $fullTree[$oCnt0]['date'] = $street['createdAt'];
            $fullTree[$oCnt0]['house'] = '';

            $houses = House::find()
                ->select('*')
                ->where(['streetUuid' => $street['uuid']])
                ->orderBy('number')
                ->all();
            $oCnt1 = 0;
            foreach ($houses as $house) {
                $fullTree[$oCnt0][$c][$oCnt1]['title']
                    = Html::a(
                    $house['street']->title.', '.$house['number'],
                    ['house/view', '_id' => $house['_id']]
                );
                $fullTree[$oCnt0][$c][$oCnt1]['type'] = 'Дом';
                $fullTree[$oCnt0][$c][$oCnt1]['date'] = $house['createdAt'];
                $fullTree[$oCnt0][$c][$oCnt1]['house'] = $house['number'];
                $fullTree[$oCnt0][$c][$oCnt1]['street'] = $street['title'];

                if ($house['houseStatusUuid'] == '111') {
                    $class = 'critical1';
                } elseif ($house['houseStatusUuid'] == '222') {
                    $class = 'critical2';
                } else {
                    $class = 'critical3';
                }
                $fullTree[$oCnt0][$c][$oCnt1]['status'] = '<div class="progress"><div class="'
                    . $class . '">' . $house['houseStatus']->title . '</div></div>';
                $subject = Subject::find()
                    ->select('*')
                    ->where(['houseUuid' => $house['uuid']])
                    ->one();
                $fullTree[$oCnt0][$c][$oCnt1]['resident'] = $subject['title'];

                $flats = Flat::find()
                    ->select('*')
                    ->where(['houseUuid' => $house['uuid']])
                    ->orderBy('number')
                    ->all();
                $oCnt2 = 0;
                foreach ($flats as $flat) {
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['title']
                        = Html::a(
                        $street['title'].', '.$house['number'].'-'.$flat['number'],
                        ['flat/view', '_id' => $flat['_id']]
                    );
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['type'] = 'Квартира';
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['date'] = $flat['createdAt'];
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['house'] = $house['number'];
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['street'] = $street['title'];
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['flat'] = $flat['number'];

                    if ($flat['flatStatusUuid'] == '111') {
                        $class = 'critical1';
                    } elseif ($flat['flatStatusUuid'] == '222') {
                        $class = 'critical2';
                    } else {
                        $class = 'critical3';
                    }
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['status'] = '<div class="progress"><div class="'
                        . $class . '">' . $flat['flatStatus']->title . '</div></div>';

                    $resident = Resident::find()
                        ->select('*')
                        ->where(['flatUuid' => $flat['uuid']])
                        ->one();
                    $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2]['resident'] = $resident['owner'];

                    $equipments = Equipment::find()
                        ->select('*')
                        ->where(['flatUuid' => $flat['uuid']])
                        ->all();
                    $eCnt = 0;
                    foreach ($equipments as $equipment) {
                        $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt]['title']
                            = Html::a(
                            $equipment['title'],
                            ['equipment/view', '_id' => $equipment['_id']]
                        );
                        $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt]['type'] = $equipment['type']->title;
                        $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt]['date'] = $equipment['date'];
                        $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt]['house'] = $house['title'];
                        $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt]['flat'] = $flat['title'];
                        $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt]['serial'] = $equipment['serial'];

                        if ($equipment['equipmentStatusUuid'] == '111') {
                            $class = 'critical1';
                        } elseif ($equipment['equipmentStatusUuid'] == '222') {
                            $class = 'critical2';
                        } else {
                            $class = 'critical3';
                        }
                        $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt]['status'] = '<div class="progress"><div class="'
                            . $class . '">' . $flat['status']->title . '</div></div>';

                        $measure = Measure::find()
                            ->select('*')
                            ->orderBy('date DESC')
                            ->where(['equipmentUuid' => $equipment['uuid']])
                            ->one();
                        $fullTree[$oCnt0][$c][$oCnt1][$c][$oCnt2][$c][$eCnt]['value'] = $measure['value'];

                        $eCnt++;
                    }
                    $oCnt2++;
                }
                $oCnt1++;
            }
            $oCnt0++;
        }
        return $this->render(
            'tree',
            [
                'objects' => $fullTree
            ]
        );
    }

    /**
     * Updates an existing Resident model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            // сохраняем модель
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render(
                    'update',
                    [
                        'model' => $model,
                    ]
                );
            }
        } else {
            return $this->render(
                'update',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Deletes an existing Resident model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Resident model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Resident the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Resident::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
