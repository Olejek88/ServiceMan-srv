<?php
namespace backend\controllers;

use app\commands\MainFunctions;
use backend\models\EquipmentSearch;
use common\models\Equipment;
use common\models\EquipmentStatus;
use common\models\EquipmentType;
use common\models\Flat;
use common\models\House;
use common\models\Measure;
use common\models\Message;
use common\models\PhotoEquipment;
use common\models\Street;
use common\models\User;
use common\models\UserHouse;
use common\models\Users;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * EquipmentController implements the CRUD actions for Equipment model.
 */
class EquipmentController extends Controller
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
                'class' => VerbFilter::class,
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
     * Lists all Equipment models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Equipment::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'serial') {
                $model['serial'] = $_POST['Equipment'][$_POST['editableIndex']]['serial'];
            }
            if ($_POST['editableAttribute'] == 'equipmentTypeUuid') {
                $model['equipmentTypeUuid'] = $_POST['Equipment'][$_POST['editableIndex']]['equipmentTypeUuid'];
            }
            if ($_POST['editableAttribute'] == 'equipmentStatusUuid') {
                $model['equipmentStatusUuid'] = $_POST['Equipment'][$_POST['editableIndex']]['equipmentStatusUuid'];
            }
            if ($_POST['editableAttribute'] == 'testDate') {
                $model['testDate'] = date("Y-m-d H:i:s", $_POST['Equipment'][$_POST['editableIndex']]['testDate']);
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new EquipmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single Equipment model.
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
     * Creates a new Equipment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Equipment();

        if ($model->load(Yii::$app->request->post())) {
            // проверяем все поля, если что-то не так показываем форму с ошибками
            if (!$model->validate()) {
                return $this->render('create', ['model' => $model]);
            }
            // сохраняем запись
            if ($model->save(false)) {
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render('create', ['model' => $model]);
            }
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    /**
     * Creates a new Equipment models.
     *
     * @return mixed
     */
    public function actionNew()
    {
        $equipments = array();
        $equipment_count=0;
        $flats = Flat::find()
            ->select('*')
            ->all();
        foreach ($flats as $flat) {
            $equipment = Equipment::find()
                ->select('*')
                ->where(['flatUuid' => $flat['uuid']])
                ->one();
            if ($equipment==null) {
                $equipment = new Equipment();
                $equipment->uuid = MainFunctions::GUID();
                $equipment->houseUuid = $flat['house']->uuid;
                $equipment->flatUuid = $flat['uuid'];
                $equipment->equipmentTypeUuid = EquipmentType::EQUIPMENT_HVS;
                $equipment->equipmentStatusUuid = EquipmentStatus::UNKNOWN;
                $equipment->serial = '222222';
                $equipment->testDate = date('Y-m-d H:i:s');
                $equipment->changedAt = date('Y-m-d H:i:s');
                $equipment->createdAt = date('Y-m-d H:i:s');
                $equipment->save();
                $equipments[$equipment_count] = $equipment;
                $equipment_count++;
            }
            else {
                if ($equipment['equipmentTypeUuid'] != EquipmentType::EQUIPMENT_HVS) {
                    $equipment['equipmentTypeUuid'] = EquipmentType::EQUIPMENT_HVS;
                    $equipment['changedAt'] = date('Y-m-d H:i:s');
                    $equipment->save();
                    echo $equipment['uuid'] . '<br/>';
                }
            }
        }
        //return $this->render('new', ['equipments' => $equipments]);
    }


    /**
     * Updates an existing Equipment model.
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
     * Build tree of equipment
     *
     * @return mixed
     */
    public function actionTree()
    {
        $c = 'children';
        $fullTree = array();
        $types = EquipmentType::find()
            ->select('*')
            ->orderBy('title')
            ->all();
        $oCnt0 = 0;
        foreach ($types as $type) {
            $fullTree[$oCnt0]['title'] = Html::a(
                $type['title'],
                ['equipment-type/view', 'id' => $type['_id']]
            );
            $equipments = Equipment::find()
                ->select('*')
                ->where(['equipmentTypeUuid' => $type['uuid']])
                ->orderBy('serial')
                ->all();
            $oCnt1 = 0;
            foreach ($equipments as $equipment) {
                $fullTree[$oCnt0][$c][$oCnt1]['title']
                    = Html::a(
                    'ул.' . $equipment['house']['street']->title . ', д.' . $equipment['house']->number . ', кв.' . $equipment['flat']->number,
                    ['equipment/view', 'id' => $equipment['_id']]
                );
                if ($equipment['equipmentStatusUuid'] == EquipmentStatus::NOT_MOUNTED) {
                    $class = 'critical1';
                } elseif ($equipment['equipmentStatusUuid'] == EquipmentStatus::NOT_WORK) {
                    $class = 'critical2';
                } else {
                    $class = 'critical3';
                }
                $fullTree[$oCnt0][$c][$oCnt1]['status'] = '<div class="progress"><div class="'
                    . $class . '">' . $equipment['equipmentStatus']->title . '</div></div>';
                $fullTree[$oCnt0][$c][$oCnt1]['date'] = $equipment['testDate'];
                $fullTree[$oCnt0][$c][$oCnt1]['serial'] = $equipment['serial'];

                $measure = Measure::find()
                    ->select('*')
                    ->where(['equipmentUuid' => $equipment['uuid']])
                    ->orderBy('date DESC')
                    ->one();
                if ($measure) {
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_date'] = $measure['date'];
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_value'] = $measure['value'];
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_user'] = $measure['user']->name;
                } else {
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_date'] = $equipment['changedAt'];
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_value'] = "не снимались";
                    $fullTree[$oCnt0][$c][$oCnt1]['measure_user'] = "-";
                }

                $photo = PhotoEquipment::find()
                    ->select('*')
                    ->where(['equipmentUuid' => $equipment['uuid']])
                    ->orderBy('createdAt DESC')
                    ->one();
                if ($photo) {
                    $fullTree[$oCnt0][$c][$oCnt1]['photo_date'] = $photo['createdAt'];
                    $fullTree[$oCnt0][$c][$oCnt1]['photo'] = Html::a(
                        '<img width="100px" src="/storage/equipment/'.$photo['uuid'].'.jpg" />',
                        ['storage/equipment/'.$photo['uuid'].'.jpg']
                    );
                    $fullTree[$oCnt0][$c][$oCnt1]['photo_user'] = $photo['user']->name;
                }
                else {
                    $fullTree[$oCnt0][$c][$oCnt1]['photo_date'] = 'нет фото';
                    $fullTree[$oCnt0][$c][$oCnt1]['photo'] = '-';
                    $fullTree[$oCnt0][$c][$oCnt1]['photo_user'] = '-';
                }
                $oCnt1++;
            }
            $oCnt0++;
        }
        return $this->render(
            'tree',
            ['equipment' => $fullTree]
        );
    }

    /**
     * Build tree of equipment by user
     *
     * @return mixed
     */
    public function actionTreeUser()
    {
        $c = 'children';
        $fullTree = array();
        $users = Users::find()
            ->select('*')
            ->where('name != "sUser"')
            ->andWhere('name != "Иванов О.А."')
            ->orderBy('_id')
            ->all();
        $oCnt0 = 0;
        foreach ($users as $user) {
            $fullTree[$oCnt0]['title'] = Html::a(
                $user['name'],
                ['user/view', 'id' => $user['_id']]
            );
/*            $query = Equipment::find()
                ->select('*')
                ->where(['flatUuid' => (
                    Flat::find()->select('uuid')->where(['houseUuid' => (
                        UserHouse::find()->select('houseUuid')->where(['userUuid' => $user['uuid']])->all()
                    )]))]);
            //$query->with('house');
            $equipments = $query->orderBy('changedAt')->groupBy('flatUuid')->all();*/
            $oCnt1 = 0;
            $measure_total_count = 0;
            $measure_count = 0;
            $photo_count = 0;
            $message_count = 0;
            $user_houses = UserHouse::find()->select('houseUuid')->where(['userUuid' => $user['uuid']])->all();
            foreach ($user_houses as $user_house) {
                $flats = Flat::find()->select('uuid')->where(['houseUuid' => $user_house['houseUuid']])->all();
                foreach ($flats as $flat) {
                    $equipment = Equipment::find()
                        ->select('*')
                        ->where(['flatUuid' => $flat['uuid']])
                        ->orderBy('changedAt desc')
                        ->one();
                    if ($equipment) {
                        $fullTree[$oCnt0][$c][$oCnt1]['title']
                            = Html::a(
                            'ул.' . $equipment['house']['street']->title . ', д.' .
                            $equipment['house']->number . ', кв.' . $equipment['flat']->number,
                            ['equipment/view', 'id' => $equipment['_id']]
                        );

                        $message = Message::find()
                            ->select('*')
                            ->orderBy('date DESC')
                            ->where(['flatUuid' => $equipment['flat']['uuid']])
                            ->one();
                        if ($message != null) {
                            $fullTree[$oCnt0][$c][$oCnt1]['message'] = substr($message['message'], 0, 150);
                            $message_count++;
                        }

                        $photo = PhotoEquipment::find()
                            ->select('*')
                            ->where(['equipmentUuid' => $equipment['uuid']])
                            ->orderBy('createdAt DESC')
                            ->one();
                        if ($photo) {
                            $fullTree[$oCnt0][$c][$oCnt1]['photo_date'] = $photo['createdAt'];
                            $fullTree[$oCnt0][$c][$oCnt1]['photo'] = Html::a('фото',
                                ['storage/equipment/' . $photo['uuid'] . '.jpg']
                            );
                            $fullTree[$oCnt0][$c][$oCnt1]['photo_user'] = $photo['user']->name;
                            $photo_count++;
                        } else {
                            $fullTree[$oCnt0][$c][$oCnt1]['photo_date'] = 'нет фото';
                            $fullTree[$oCnt0][$c][$oCnt1]['photo'] = '-';
                            $fullTree[$oCnt0][$c][$oCnt1]['photo_user'] = '-';
                        }

                        $measure = Measure::find()
                            ->select('*')
                            ->where(['equipmentUuid' => $equipment['uuid']])
                            ->orderBy('date DESC')
                            ->one();
                        if ($measure) {
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_date'] = $measure['date'];
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_value'] = $measure['value'];
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_user'] = $measure['user']->name;
                            $measure_total_count++;
                        } else {
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_date'] = $equipment['changedAt'];
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_value'] = "не снимались";
                            $fullTree[$oCnt0][$c][$oCnt1]['measure_user'] = "-";
                        }

                        if ($equipment['equipmentStatusUuid'] == EquipmentStatus::NOT_MOUNTED) {
                            $class = 'critical1';
                        } elseif ($equipment['equipmentStatusUuid'] == EquipmentStatus::NOT_WORK) {
                            $class = 'critical2';
                        } elseif ($equipment['equipmentStatusUuid'] == EquipmentStatus::UNKNOWN) {
                            $class = 'critical4';
                        } else {
                            $class = 'critical3';
                        }

                        $status = $equipment['equipmentStatus']->title;
                        if ($measure) {
                            //echo $measure['date'].' | '.time() .'-'. strtotime($measure['date']). ' < ' . (3600 * 24 * 7 * 1).'<br/>';
                            if (time() - strtotime($measure['date']) > (3600 * 24 * 7 * 1)) {
                                $class = 'critical2';
                                $status = 'Посещался';
                            } else
                                $measure_count++;
                        } else {
                            if ($message != null) {
                                $class = 'critical4';
                                $status = 'Не попали';
                            } else {
                                if ($photo==null) {
                                    $class = 'critical1';
                                    $status = 'Не посещался';
                                } else {
                                    $class = 'critical2';
                                    $status = 'Нет показаний';
                                }
                            }
                        }

                        $fullTree[$oCnt0][$c][$oCnt1]['status'] = '<div class="progress"><div class="'
                            . $class . '">' . $status . '</div></div>';

                        $fullTree[$oCnt0][$c][$oCnt1]['date'] = $equipment['testDate'];
                        $fullTree[$oCnt0][$c][$oCnt1]['serial'] = $equipment['serial'];

                        $oCnt1++;
                    }
                }
                if ($oCnt1 > 0) {
                    if ($oCnt1 > 0) {
                        $ok = $measure_count * 100 / $oCnt1;
                        if ($ok < 20) {
                            $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical1">Очень плохо</div></div>';
                        } elseif ($ok < 45) {
                            $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical2">Плохо</div></div>';
                        } elseif ($ok < 70) {
                            $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical4">Средне</div></div>';
                        } else {
                            $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical3">Хорошо</div></div>';
                        }
                    }
                    $fullTree[$oCnt0]['measure_date'] = 'Показаний: ' . $measure_count . '[' . $measure_total_count . ']';
                    $fullTree[$oCnt0]['measure_value'] = $measure_count . '[' . number_format($measure_count * 100 / $oCnt1, 2) . '%]';
                    $fullTree[$oCnt0]['photo'] = $photo_count . '[' . number_format($photo_count * 100 / $oCnt1, 2) . '%]';
                    $fullTree[$oCnt0]['message'] = $message_count . '[' . number_format($message_count * 100 / $oCnt1, 2) . '%]';
                }
            }
            $oCnt0++;
        }
        return $this->render(
            'tree-user',
            ['equipment' => $fullTree]
        );
    }

    /**
     * Build tree of equipment by user
     *
     * @return mixed
     */
    public function actionTreeStreet()
    {
        $c = 'children';
        $fullTree = array();
        $streets = Street::find()
            ->select('*')
            ->orderBy('title')
            ->all();
        $oCnt0 = 0;
        foreach ($streets as $street) {
            $last_user='';
            $last_date='';
            $house_count=0;
            $house_visited=0;
            $photo_count=0;
            $fullTree[$oCnt0]['title'] = Html::a(
                $street['title'],
                ['street/view', 'id' => $street['_id']]
            );
            $oCnt1 = 0;
            $houses = House::find()->select('uuid,number')->where(['streetUuid' => $street['uuid']])->
                orderBy('number')->all();
            foreach ($houses as $house) {
                $user_house = UserHouse::find()->select('_id')->where(['houseUuid' => $house['uuid']])->one();
                $user = Users::find()->where(['uuid' =>
                        UserHouse::find()->where(['houseUuid' => $house['uuid']])->one()
                ])->one();
                $flats = Flat::find()->select('uuid,number')->where(['houseUuid' => $house['uuid']])->all();
                foreach ($flats as $flat) {
                    $house_count++;
                    $visited=0;
                    $equipments = Equipment::find()->where(['flatUuid' => $flat['uuid']])->all();
                    foreach ($equipments as $equipment) {
                        $fullTree[$oCnt0][$c][$oCnt1]['title']
                            = Html::a(
                            'ул.' . $equipment['house']['street']->title . ', д.' . $equipment['house']->number . ', кв.' . $equipment['flat']->number,
                            ['equipment/view', 'id' => $equipment['_id']]
                        );

                        if ($user != null)
                            $fullTree[$oCnt0][$c][$oCnt1]['user'] = Html::a(
                                $user['name'],
                                ['user-house/delete', 'id' => $user_house['_id']], ['target' => '_blank']
                            );

                        if ($equipment['equipmentStatusUuid'] == EquipmentStatus::NOT_MOUNTED) {
                            $class = 'critical1';
                        } elseif ($equipment['equipmentStatusUuid'] == EquipmentStatus::NOT_WORK) {
                            $class = 'critical2';
                        } elseif ($equipment['equipmentStatusUuid'] == EquipmentStatus::UNKNOWN) {
                            $class = 'critical4';
                        } else {
                            $class = 'critical3';
                        }
                        $fullTree[$oCnt0][$c][$oCnt1]['status'] = '<div class="progress"><div class="'
                            . $class . '">' . $equipment['equipmentStatus']->title . '</div></div>';
                        $fullTree[$oCnt0][$c][$oCnt1]['date'] = $equipment['testDate'];
                        //$fullTree[$oCnt0][$c][$oCnt1]['serial'] = $equipment['serial'];

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

                        $message = Message::find()
                            ->select('*')
                            ->orderBy('date DESC')
                            ->where(['flatUuid' => $equipment['flat']['uuid']])
                            ->one();
                        if ($message != null) {
                            $fullTree[$oCnt0][$c][$oCnt1]['message'] = substr($message['message'], 0, 150);
                            if ($visited==0)
                                $visited=1;
                                $house_visited++;
                        }

                        $photo = PhotoEquipment::find()
                            ->select('*')
                            ->where(['equipmentUuid' => $equipment['uuid']])
                            ->orderBy('createdAt DESC')
                            ->one();
                        if ($photo) {
                            $fullTree[$oCnt0][$c][$oCnt1]['photo_date'] = $photo['createdAt'];
                            $fullTree[$oCnt0][$c][$oCnt1]['photo'] = Html::a('фото',
                                ['storage/equipment/'.$photo['uuid'].'.jpg']
                            );
                            $fullTree[$oCnt0][$c][$oCnt1]['photo_user'] = $photo['user']->name;
                            $last_user = $photo['user']->name;
                            $photo_count++;
                            if ($visited==0) {
                                $visited=1;
                                $house_visited++;
                            }
                        }
                        else {
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
            $ok=0;
            if ($house_count>0)
                $ok = $house_visited*100/$house_count;
            if ($ok>100) $ok=100;
            if ($ok<20) {
                $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical1">'.
                    number_format($ok,2).'%</div></div>';
            } elseif ($ok<45) {
                $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical2">'.
                    number_format($ok,2).'%</div></div>';
            } elseif ($ok<70) {
                $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical4">'.
                    number_format($ok,2).'%</div></div>';
            } else {
                $fullTree[$oCnt0]['status'] = '<div class="progress"><div class="critical3">'.
                    number_format($ok,2).'%</div></div>';
            }
            $oCnt0++;
        }
        return $this->render(
            'tree-street',
            ['equipment' => $fullTree]
        );
    }

    /**
     * Deletes an existing Equipment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public
    function actionDelete($id)
    {
        $equipment = $this->findModel($id);
        $photos = PhotoEquipment::find()
            ->select('*')
            ->where(['equipmentUuid' => $equipment['uuid']])
            ->all();
        foreach ($photos as $photo) {
            $photo->delete();
        }

        $measures = Measure::find()
                ->select('*')
                ->where(['equipmentUuid' => $equipment['uuid']])
                ->all();
        foreach ($measures as $measure) {
            $measure->delete();
        }

        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Equipment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Equipment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel($id)
    {
        if (($model = Equipment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
