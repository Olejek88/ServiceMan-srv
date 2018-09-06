<?php

namespace api\controllers;

use api\models\form\LoginForm;
use yii\rest\Controller;

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
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRequest()
    {
        $model = new LoginForm();
        $model->load(\Yii::$app->request->bodyParams, '');
        if ($model->validate()) {
            $user = $model->getUser();
            $token = $user->generateAccessToken(60*60*24*7  );
            $user->save();

            return [
                'user_id' => $user->id,
                'token' => $token,
            ];
        } else {
            return $model;
        }
    }
}
