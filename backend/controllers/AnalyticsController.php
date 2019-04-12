<?php

namespace backend\controllers;

use common\components\MainFunctions;
use common\models\Operation;
use common\models\WorkStatus;
use common\models\OperationTemplate;
use common\models\Task;
use common\models\Users;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;


class AnalyticsController extends Controller
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

    public function getBar($time, $normative)
    {
        $difference = 100;
        if ($normative > 0)
            $difference = intval(($time - $normative) / $normative);
        $fullTree = '<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="' . $difference . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $difference . '%; background-color: ';
        if ($difference > 100) $fullTree .= '#ee2222';
        if ($difference > 80 && $difference <= 100) $fullTree .= '#2222dd';
        if ($difference <= 80) $fullTree .= '#22dd22';
        $fullTree .= ';"></div><span class="progress-completed">' . $difference . '%</span></div>';
        return $fullTree;
    }

    public function getBar2($end, $start, $normative)
    {
        $difference = 100;
        if ($normative > 0)
            $difference = intval((MainFunctions::getOperationLength($start, $end, $normative * 50) - $normative) * 100 / $normative);
        $fullTree = '<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="' . $difference . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $difference . '%; background-color: ';
        if ($difference > 100) $fullTree .= '#ee2222;';
        if ($difference > 80 && $difference <= 100) $fullTree .= '#2222dd;';
        if ($difference <= 80) $fullTree .= '#22dd22;';
        $fullTree .= '"></div><span class="progress-completed" ';
        if ($difference > 80) $fullTree .= ' style="color: #ffffff"';
        $fullTree .= '>' . $difference . '%</span></div>';
        return $fullTree;
    }

    public function actionUsers()
    {
        $taskTemplatesType[] = '';
        $treeUsersCnt[] = '';
        $treeTypesCnt[] = '';

        $taskStatus_completed = WorkStatus::find()->select('*')
            ->where(['title' => 'Выполнена'])
            ->all();
        if (!empty($taskStatus_completed[0]['uuid'])) {
            $taskStatus_completed_uuid = $taskStatus_completed[0]['uuid'];
        } else {
            $taskStatus_completed_uuid = '';
        }

        $operationStatus_completed = WorkStatus::find()->select('*')
            ->where(['title' => 'Выполнена'])
            ->all();
        if (!empty($operationStatus_completed[0]['uuid'])) {
            $operationStatus_completed_uuid = $operationStatus_completed[0]['uuid'];
        } else {
            $operationStatus_completed_uuid = '';
        }

        $operationTemplates = OperationTemplate::find()
            ->select('*')
            ->all();
        $operations = Operation::find()
            ->select('*')
            ->all();
        $tasks = Task::find()
            ->select('*')
            ->all();

        $fullTree = array();

        $allUsers = Users::find()->select('*')->all();
        $userCnt = 0;
        foreach ($allUsers as $user) {
            $fullTree[$userCnt]["name"] = $user['name'];
            $fullTree[$userCnt]["who"] = $user['whoIs'];

            $fullTree[$userCnt]["time"] = $sumTime;
            $userCnt++;
        }

        //var_dump($fullTree);
        return $this->render('users', [
            'orders' => $fullTree
        ]);
    }
}
