<?php

namespace backend\controllers;

use backend\models\ObjectsSearch;
use common\components\MainFunctions;
use common\models\Equipment;
use common\models\House;
use common\models\Measure;
use common\models\ObjectContragent;
use common\models\Objects;
use common\models\Photo;
use common\models\Street;
use common\models\UserHouse;
use common\models\Users;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Html;
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

    public function init()
    {

        if (\Yii::$app->getUser()->isGuest) {
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
                    'folder' => true
                ];
                $objects = Objects::find()->where(['houseUuid' => $house['uuid']])->all();
                foreach ($objects as $object) {
                    $childIdx2 = count($fullTree['children'][$childIdx]['children']) - 1;
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][] = [
                        'title' => $object['objectType']['title'].' '.$object['title'],
                        'folder' => true
                    ];
                    $contragents = ObjectContragent::find()->where(['objectUuid' => $object['uuid']])->all();
                    foreach ($contragents as $contragent) {
                        $childIdx3 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                        $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] = [
                            'title' => $contragent['title'],
                            'folder' => true
                        ];
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
     * Build tree of equipment by user
     *
     * @return mixed
     */
    public function actionTrees()
    {
        ini_set('memory_limit', '-1');
        $c = 'children';
        $fullTree = array();
        $streets = Street::find()
            ->select('*')
            ->orderBy('title')
            ->all();
        $oCnt0 = 0;
        foreach ($streets as $street) {
            $last_user = '';
            $last_date = '';
            $house_count = 0;
            $house_visited = 0;
            $photo_count = 0;
            $fullTree[$oCnt0]['title'] = $street['title'];
            $oCnt1 = 0;
            $houses = House::find()->select('uuid,number')->where(['streetUuid' => $street['uuid']])->
            orderBy('number')->all();
            foreach ($houses as $house) {
                $user_house = UserHouse::find()->select('_id')->where(['houseUuid' => $house['uuid']])->one();
                $user = Users::find()->where(['uuid' =>
                    UserHouse::find()->where(['houseUuid' => $house['uuid']])->one()
                ])->one();
                $objects = Objects::find()->select('uuid,title')->where(['houseUuid' => $house['uuid']])->all();
                foreach ($objects as $object) {
                    $house_count++;
                    $visited = 0;
                    $equipments = Equipment::find()->where(['objectUuid' => $object['uuid']])->all();
                    foreach ($equipments as $equipment) {
                        $fullTree[$oCnt0][$c][$oCnt1]['title']
                            = Html::a(
                            'ул.' . $equipment['house']['street']['title'] . ', д.' . $equipment['house']['number'] . ', ' . $equipment['object']['title'],
                            ['equipment/view', 'id' => $equipment['_id']]
                        );

                        if ($user != null)
                            $fullTree[$oCnt0][$c][$oCnt1]['user'] = Html::a(
                                $user['name'],
                                ['user-house/delete', 'id' => $user_house['_id']], ['target' => '_blank']
                            );
                        $status = MainFunctions::getColorLabelByStatus($equipment['equipmentStatusUuid'],'equipment_status');

                        $fullTree[$oCnt0][$c][$oCnt1]['status'] = $status;
                        $fullTree[$oCnt0][$c][$oCnt1]['date'] = $equipment['testDate'];

                        $measure = Measure::find()
                            ->select('*')
                            ->where(['equipmentUuid' => $equipment['uuid']])
                            ->orderBy('date DESC')
                            ->one();
                        if ($measure) {
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_date'] = $measure['date'];
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_value'] = $measure['value'];
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_user'] = $measure['user']->name;
                            $last_user = $measure['user']->name;
                            $last_date = $measure['date'];
                            $house_visited++;
                            $visited++;
                        } else {
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_date'] = $equipment['changedAt'];
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_value'] = "не снимались";
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_user'] = "-";
                        }

                        $photo = Photo::find()
                            ->select('*')
                            ->where(['objectUuid' => $equipment['uuid']])
                            ->orderBy('createdAt DESC')
                            ->one();
                        if ($photo) {
                            $fullTree[$oCnt0][$c][$oCnt1]['photo_date'] = $photo['createdAt'];
                            $fullTree[$oCnt0][$c][$oCnt1]['photo'] = Html::a('фото',
                                ['storage/equipment/' . $photo['uuid'] . '.jpg']
                            );
                            $fullTree[$oCnt0][$c][$oCnt1]['photo_user'] = $photo['user']->name;
                            $last_user = $photo['user']->name;
                            $photo_count++;
                            if ($visited == 0) {
                                $visited = 1;
                                $house_visited++;
                            }
                        } else {
                            $fullTree[$oCnt0][$c][$oCnt1]['photo_date'] = 'нет фото';
                            $fullTree[$oCnt0][$c][$oCnt1]['photo'] = '-';
                            $fullTree[$oCnt0][$c][$oCnt1]['photo_user'] = '-';
                        }
                        $oCnt1++;
                    }
                }
            }
            $fullTree[$oCnt0]['measure_user'] = $last_user;
            $fullTree[$oCnt0]['measure_date'] = $last_date;
            $fullTree[$oCnt0]['photo_user'] = $last_user;
            $fullTree[$oCnt0]['photo_date'] = $last_date;
            $fullTree[$oCnt0]['photo'] = $photo_count;
            $ok = 0;
            if ($house_count > 0)
                $ok = $house_visited * 100 / $house_count;
            if ($ok > 100) $ok = 100;
            if ($ok < 20) {
                $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical1">' .
                    number_format($ok, 2) . '%</div></div>';
            } elseif ($ok < 45) {
                $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical2">' .
                    number_format($ok, 2) . '%</div></div>';
            } elseif ($ok < 70) {
                $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical4">' .
                    number_format($ok, 2) . '%</div></div>';
            } else {
                $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical3">' .
                    number_format($ok, 2) . '%</div></div>';
            }
            $oCnt0++;
        }
        return $this->render(
            'tree',
            ['equipment' => $fullTree]
        );
    }

}
