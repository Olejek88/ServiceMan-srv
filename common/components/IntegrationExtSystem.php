<?php


namespace common\components;


use common\models\Request;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception;

interface IntegrationExtSystem
{
    /**
     * @param $request Request
     * @param string $text
     * @return boolean
     * @throws InvalidConfigException
     * @throws Exception
     */
    static function closeAppeal($request, $text = "");

    /**
     * @param $request Request Обращение
     * @param $text string Комментарий
     * @return int
     * @throws InvalidConfigException
     * @throws Exception
     */
    static function sendComment($request, $text);
}