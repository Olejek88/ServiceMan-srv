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
use common\models\RepairPart;
use common\models\RepairPartTypeTree;
use Yii;
use yii\base\DynamicModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

use common\models\RepairPartType;

use backend\models\RepairPartSearchType;

/**
 * RepairPartTypeController implements the CRUD actions for RepairPartType model.
 *
 * @category Category
 * @package  Backend\controllers
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class RepairPartTypeController extends Controller
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
     *
     * @throws UnauthorizedHttpException
     */
    public function init()
    {

        if (\Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    /**
     * Lists all RepairPartType models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RepairPartSearchType();
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
     * Displays a single RepairPartType model.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $parentId = TypeTreeHelper::getParentId(
            $id, RepairPartType::class, RepairPartTypeTree::class
        );
        $parentType = RepairPartType::findOne($parentId);
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
     * Creates a new RepairPartType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RepairPartType();
        $parentModel = new DynamicModel(['parentUuid']);
        $parentModel->addRule(['parentUuid'], 'string', ['max' => 45]);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $parentModel->load(Yii::$app->request->post());

            // сохраняем новый тип запчасти
            if ($model->save()) {
                $parentId = $model->_id;
                $parentUuid = $parentModel->parentUuid;
                if ($parentUuid === '00000000-0000-0000-0000-000000000000') {
                    // элемен будет в корне списка
                    $childId = $parentId;
                } else {
                    // находим id родительского типа
                    $parentType = RepairPartType::find()
                        ->where(['uuid' => $parentUuid])
                        ->one();
                    $childId = $parentType->_id;
                }

                TypeTreeHelper::addTree(
                    $parentId, $childId, RepairPartTypeTree::class
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
            $parentModel->parentUuid = '00000000-0000-0000-0000-000000000000';
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
     * Updates an existing RepairPartType model.
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
                $parentModel->parentUuid,
                RepairPartType::class,
                RepairPartTypeTree::class
            );
            $model->save();
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            // открываем форму для редактирования
            $parentModel = new DynamicModel(['parentUuid']);
            $parentModel->addRule(['parentUuid'], 'string', ['max' => 45]);
            // получаем id родителя
            $parentId = TypeTreeHelper::getParentId(
                $id, RepairPartType::class, RepairPartTypeTree::class
            );
            if ($parentId > 0) {
                $parentUuid = RepairPartType::findOne($parentId)->uuid;
            } else {
                $parentUuid = '00000000-0000-0000-0000-000000000000';
            }

            $parentModel->parentUuid = $parentUuid;
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
     * Deletes an existing RepairPartType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $type = RepairPartType::findOne($id);

        // можно перевесить потомков под родителя удаляемого элемента
        // может вообще дать возможность удалять целиком ветку?

        // проверяем на наличие запчастей с таким типом
        $items = RepairPart::find()
            ->where(['repairPartTypeUuid' => $type->uuid])->all();
        if (count($items) > 0) {
            //return $this->redirect(['index']);
            $msg = 'Невозможно удалить, так как есть запчасти данного типа!';
            return $this->render(
                'delete',
                [
                    'message' => $msg,
                ]
            );
        }

        // проверяем на наличие потомков
        $children = RepairPartTypeTree::find()->where(['parent' => $id])->all();
        if (count($children) == 1) {
            // удаляем ссылки на родителей
            // т.е. одну ссылку где наш элемент является сам себе
            // и родителем и потомком, и все ссылки где он потомок
            // от других типов
            RepairPartTypeTree::deleteAll(['child' => $id]);
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
     * Finds the RepairPartType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return RepairPartType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RepairPartType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
