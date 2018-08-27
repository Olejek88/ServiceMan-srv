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

use common\components\TypeTreeHelper;
use common\models\DefectType;
use common\models\EquipmentModel;
use common\models\EquipmentTypeTree;
use Yii;
use yii\base\DynamicModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

use common\models\EquipmentType;

use backend\models\EquipmentSearchType;

/**
 * EquipmentTypeController implements the CRUD actions for EquipmentType model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class EquipmentTypeController extends Controller
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
     * Lists all StageTemplate models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EquipmentSearchType();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 100;

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single EquipmentType model.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $parentId = TypeTreeHelper::getParentId(
            $id, EquipmentType::class, EquipmentTypeTree::class
        );
        $parentType = EquipmentType::findOne($parentId);
        if ($parentType) {
            $parentType = $parentType->title;
        } else {
            $parentType = 'Корень';
        }

        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
                'parentType' => $parentType,
            ]
        );
    }

    /**
     * Creates a new EquipmentType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EquipmentType();
        $parentModel = new DynamicModel(['parentUuid']);
        $parentModel->addRule(['parentUuid'], 'string', ['max' => 45]);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $parentModel->load(Yii::$app->request->post());

            // сохраняем новый тип инструмента
            if ($model->save()) {
                $parentId = $model->_id;
                $parentUuid = $parentModel['parentUuid'];
                if ($parentUuid === '00000000-0000-0000-0000-000000000000') {
                    // элемен будет в корне списка
                    $childId = $parentId;
                } else {
                    // находим id родительского типа
                    $parentType = EquipmentType::find()
                        ->where(['uuid' => $parentUuid])
                        ->one();
                    $childId = $parentType['_id'];
                }

                TypeTreeHelper::addTree(
                    $parentId, $childId, EquipmentTypeTree::class
                );

                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render(
                    'create',
                    [
                        'model' => $model,
                        'parentModel' => $parentModel,
                    ]
                );
            }
        } else {
            $parentModel['parentUuid'] = '00000000-0000-0000-0000-000000000000';
            return $this->render(
                'create',
                [
                    'model' => $model,
                    'parentModel' => $parentModel,
                ]
            );
        }
    }

    /**
     * Updates an existing EquipmentType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isPost) {
            // $model->load(Yii::$app->request->post()) && $model->save()
            $model->load(Yii::$app->request->post());
            $parentModel = new DynamicModel(['parentUuid']);
            $parentModel->addRule(['parentUuid'], 'string', ['max' => 45]);
            $parentModel->load(Yii::$app->request->post());
            TypeTreeHelper::moveTree(
                $id,
                $parentModel['parentUuid'],
                EquipmentType::class,
                EquipmentTypeTree::class
            );
            $model->save();
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            // открываем форму для редактирования
            $parentModel = new DynamicModel(['parentUuid']);
            $parentModel->addRule(['parentUuid'], 'string', ['max' => 45]);
            // получаем id родителя
            $parentId = TypeTreeHelper::getParentId(
                $id, EquipmentType::class, EquipmentTypeTree::class
            );
            if ($parentId > 0) {
                $parentUuid = EquipmentType::findOne($parentId)->uuid;
            } else {
                $parentUuid = '00000000-0000-0000-0000-000000000000';
            }

            $parentModel['parentUuid'] = $parentUuid;
            return $this->render(
                'update',
                [
                    'parentModel' => $parentModel,
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Deletes an existing EquipmentType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $type = EquipmentType::findOne($id);

        // можно перевесить потомков под родителя удаляемого элемента
        // может вообще дать возможность удалять целиком ветку?

        // проверяем на наличие моделей с таким типом
        $items = EquipmentModel::find()
            ->where(['equipmentTypeUuid' => $type->uuid])->all();
        if (count($items) > 0) {
            $msg = 'Невозможно удалить, так как есть модели данного типа!';
            return $this->render(
                'delete',
                [
                    'message' => $msg,
                ]
            );
        }

        // проверяем на наличие дефектов с таким типом
        $items = DefectType::find()
            ->where(['equipmentTypeUuid' => $type->uuid])->all();
        if (count($items) > 0) {
            $msg = 'Невозможно удалить, так как есть дефекты данного типа!';
            return $this->render(
                'delete',
                [
                    'message' => $msg,
                ]
            );
        }

        // проверяем на наличие потомков
        $children = EquipmentTypeTree::find()->where(['parent' => $id])->all();
        if (count($children) == 1) {
            // удаляем ссылки на родителей
            // т.е. одну ссылку где наш элемент является сам себе
            // и родителем и потомком, и все ссылки где он потомок
            // от других типов
            EquipmentTypeTree::deleteAll(['child' => $id]);
            // удаляем сам тип
            $this->findModel($id)->delete();
        } else {
            $msg = 'Невозможно удалить, так как есть потомки у этого типа!';
            return $this->render(
                'delete',
                [
                    'message' => $msg,
                ]
            );
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipmentType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return EquipmentType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipmentType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
