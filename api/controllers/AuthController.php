<?php

namespace api\controllers;

use Yii;
use api\models\form\LoginForm;
use yii\rest\Controller;
use yii\web\Response;
use yii\base\InvalidConfigException;

class AuthController extends Controller
{
    /**
     * @inheritdoc
     */
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['request'] = ['POST', 'OPTIONS'];
        return $verbs;
    }

    /**
     * @return LoginForm|array
     * @throws InvalidConfigException
     */
    public function actionRequest()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if ($model->validate()) {
            $user = $model->getUser();
            if ($user != null) {
                $token = $user->generateAccessToken(60 * 60 * 24 * 7);
                $user->save();
                return [
                    'usersUuid' => $user->getUsers()->uuid,
                    'token' => $token,
                ];
            } else {
                return $model;
            }
        } else {
            return $model;
        }
    }

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_HTML;
        return $this->render('index');
    }
}
