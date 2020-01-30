<?php

namespace api\controllers;

use common\components\MainFunctions;
use common\components\ZhkhActiveRecord;
use common\models\Comments;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\ExtSystemUser;
use common\models\House;
use common\models\ObjectContragent;
use common\models\Objects;
use common\models\ObjectType;
use common\models\Organization;
use common\models\OrganizationSub;
use common\models\Request;
use common\models\RequestStatus;
use common\models\RequestType;
use common\models\Settings;
use common\models\Users;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\httpclient\Client;
use yii\rest\Controller;
use yii\web\Response;

class IntegrationIsController extends Controller
{
    public const IS_API_PARAM_NAME = 'IS_API';
    public const IS_API_TOKEN_NAME = 'IS_TOKEN';

    public const IS_APPEAL_NEW = 1;
    public const IS_APPEAL_IN_WORK = 2;
    public const IS_APPEAL_CLOSED = 3;

    public const LOG_TAG = 'Intersvyaz integration';

    /**
     * Behaviors
     *
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return array_merge(
            $behaviors,
            ['verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'notify' => ['post'],
                ],
            ],
                'contentNegotiator' => [
                    'class' => ContentNegotiator::class,
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON
                    ]
                ],
            ]
        );

    }

    /**
     * Actions
     *
     * @return array
     */
    public function actions()
    {
        $actions = [
            'notify',
        ];
        return $actions;
    }

    /**
     * @param $orgUuid
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function actionNotify($orgUuid)
    {
        $request = Yii::$app->request;
        $headers = $request->getHeaders();
        $signature = $headers->get('X-Signature');
        $rawBody = $request->getRawBody();
        file_put_contents(Yii::getAlias('@api/runtime/logs/is-' . date('Ymd-His') . ' - ' . rand(0, 65535) . '.log'),
            $rawBody . PHP_EOL . '"X-Signature: ' . $signature . '"');

        $organisation = Organization::findOne(['uuid' => $orgUuid]);
        if ($organisation == null) {
            Yii::error('Не найдена организация с uuid: ' . $orgUuid, self::LOG_TAG);
            return [];
        }

        $IS_API = self::getOrgSetting($orgUuid, self::IS_API_PARAM_NAME);
        $IS_API = json_decode($IS_API->parameter, true);

        $secret = $IS_API['secret'];
        $testSignature = hash_hmac('sha256', $rawBody, $secret);
        if ($testSignature !== $signature) {
            Yii::error('Уведомление для организации (uuid=' . $orgUuid . ') имеет не верную подпись.', self::LOG_TAG);
            return [];
        }

        $operation = $request->getBodyParam('operation');
        if (!in_array($operation, ['create', 'update'])) {
            Yii::error('Уведомление для организации (uuid=' . $orgUuid . ') имеет не известную операцию('
                . $operation . ').', self::LOG_TAG);
            return [];
        }

        $object = $request->getBodyParam('object');
        if (!in_array($object, ['appeal', 'comment', 'attachment'])) {
            Yii::error('Уведомление для организации (uuid=' . $orgUuid . ') имеет не известный объект('
                . $object . ').', self::LOG_TAG);
            return [];
        }

        // достаём данные
        $data = $request->getBodyParam('data');

        switch ($operation) {
            case 'create' :
                switch ($object) {
                    case 'appeal' :
                        if (!self::createAppeal($orgUuid, $data)) {
                            Yii::error('Не смогли создать обращение для организации (uuid=' . $orgUuid . ')',
                                self::LOG_TAG);
                            return [];
                        }

                        return [];
                        break;
                    case 'comment' :
                        if (!self::createComment($data)) {
                            Yii::error('Не смогли создать комментарий для организации (uuid=' . $orgUuid . ')',
                                self::LOG_TAG);
                            return [];
                        }

                        return [];
                        break;
                    case 'attachment' :
                        // TODO: реализовать обработку объекта attachment
                        return [];
                        break;
                }
                break;
            case 'update' :
                switch ($object) {
                    case 'appeal' :
                        if (!self::updateAppeal($orgUuid, $data)) {
                            Yii::error('Не смогли обновить обращение для организации (uuid=' . $orgUuid . ')',
                                self::LOG_TAG);
                            return [];
                        }

                        return [];
                        break;
                    case 'comment' :
                        // комментарии не меняются вовсе
                        return [];
                        break;
                    case 'attachment' :
                        // вложения не меняются вовсе
                        return [];
                        break;
                }
        }

        return [];
    }

    /**
     * @param $oid string uuid организации
     * @param $data array массив с инфорацией об обращении
     * @return bool
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Exception
     */
    private function createAppeal($oid, $data)
    {
        $oids = [$oid];
        // получаем все связанные организации
        $orgSubs = OrganizationSub::find()->where(['masterUuid' => $oid])->all();
        foreach ($orgSubs as $orgSub) {
            $oids[] = $orgSub->subUuid;
        }

        // ищем дом по gis_id, только для uuid организации для кторой выполняется web-hook и связанных с ней.
        $fiasHouseUuid = strtoupper($data['address']['buildingFiasId']);
        $house = House::find()->where([
            'gis_id' => $fiasHouseUuid,
            'oid' => $oids,
        ])->one();

        if ($house == null) {
            // Интерсвязь прислала обращение, с uuid дома которого нет в базе
            // Может получится так что дом закрепили за ДСС а они его еще в базу не добавили
            // либо по какой-то причине изменился uuid
            Yii::error('Для организации с oid: ' . $oid
                . ', при создании обращения (extId=' . $data['id'] . '), не нашли дома с ним связанного '
                . '(buildingFiasId=' . $data['address']['buildingFiasId'] . ')', self::LOG_TAG);
            return false;
        }

        // uuid организации за которой закреплён дом, т.е. именно для неё будем создавать обращение
        $realOid = $house->oid;

        // ищем объект по квартире или "Общий" если квартира не указана
        $localObject = null;
        $flatNum = 0;
        if (isset($data['address']['flatNum'])) {
            $flatNum = $data['address']['flatNum'];
        }

        $isCommonObject = false;
        if ($flatNum > 0) {
            $localObject = Objects::find()->where(['title' => $flatNum, 'houseUuid' => $house->uuid])->one();
        } else {
            $localObject = Objects::find()->where([
                'title' => Objects::COMMON_OBJECT_TITLE,
                'houseUuid' => $house->uuid,
                'objectTypeUuid' => ObjectType::OBJECT_TYPE_GENERAL
            ])->one();
            $isCommonObject = true;
        }

        $localObjectUuid = $localObject != null ? $localObject->uuid : null;
        if ($localObjectUuid == null) {
            Yii::error('Для организации с oid: ' . $realOid
                . ', при создании обращения (extId=' . $data['id'] . '), не нашли объекта с ним связанного '
                . '(buildingFiasId=' . $data['address']['buildingFiasId'] . ', flatNum=' . $flatNum . ')', self::LOG_TAG);
        }

        foreach ($data['incidents'] as $incident) {
            // ищем внешнего пользователя
            $extUser = ExtSystemUser::find()->where([
                'extId' => $incident['user']['id'],
                'integrationClass' => self::class,
            ])->one();
            if ($extUser == null) {
                // создаём его
                $extUser = new ExtSystemUser();
                $extUser->uuid = MainFunctions::GUID();
                $extUser->oid = $realOid;
                $extUser->extId = '' . $incident['user']['id'];
                $extUser->fullName = $incident['user']['fullName'];
                $extUser->rawData = json_encode($incident['user']);
                $extUser->integrationClass = self::class;
                if (!$extUser->save()) {
                    Yii::error('Для организации с oid: ' . $realOid
                        . ', при создании обращения (extId=' . $data['id'] . '), не могли создать '
                        . ' внешнего пользователя (extId=' . $incident['user']['id'] . ').',
                        self::LOG_TAG);
                    return false;
                }
            }

            // объявляем переменную для связи объекта с контрагентом
            $objectContr = null;

            // ищем контрагента связанного с внешним пользователем
            $contragent = Contragent::findOne(['extSystemUserUuid' => $extUser->uuid]);
            if ($contragent == null) {
                // создаём его
                $contragent = new Contragent();
                $contragent->uuid = MainFunctions::GUID();
                $contragent->oid = $realOid;
                $contragent->title = $incident['user']['fullName'];
                $contragent->address = $incident['address']['text'];
                $contragent->phone = '' . $incident['phone'];
                $contragent->email = $incident['email'];
                $contragent->extSystemUserUuid = $extUser->uuid;
                $contragent->contragentTypeUuid = ContragentType::CITIZEN;
                if (!$contragent->save()) {
                    Yii::error('Для организации с oid: ' . $realOid
                        . ', при создании обращения (extId=' . $data['id'] . '), не могли создать '
                        . ' контрагента для внешнего пользователя (uuid=' . $extUser->uuid . ').',
                        self::LOG_TAG);
                    return false;
                }

                // если контрагента только что создали, значит связи между ним и объектом нет
                // создаём связь между объектом и контрагентом только для квартир, для общих объектов нет
                if ($localObjectUuid != null && !$isCommonObject) {
                    $objectContr = new ObjectContragent();
                    $objectContr->uuid = MainFunctions::GUID();
                    $objectContr->oid = $realOid;
                    $objectContr->objectUuid = $localObjectUuid;
                    $objectContr->contragentUuid = $contragent->uuid;
                    if (!$objectContr->save()) {
                        Yii::error('Для организации с oid: ' . $realOid
                            . ', при создании обращения (extId=' . $data['id'] . '), не могли создать связь объекта (uuid='
                            . $localObjectUuid . ') и контрагента (uuid=' . $contragent->uuid . ').',
                            self::LOG_TAG);
                        return false;
                    }
                }
            }


            if ($objectContr == null) {
                // контрагент "старый", ищем связь с объектом
                $objectContr = ObjectContragent::find()->where(['objectUuid' => $localObjectUuid])->one();
                if ($objectContr == null) {
                    // создаём связь между объектом и контрагентом только для квартир, для общих объектов нет
                    if ($localObjectUuid != null && !$isCommonObject) {
                        $objectContr = new ObjectContragent();
                        $objectContr->uuid = MainFunctions::GUID();
                        $objectContr->oid = $realOid;
                        $objectContr->objectUuid = $localObjectUuid;
                        $objectContr->contragentUuid = $contragent->uuid;
                        if (!$objectContr->save()) {
                            Yii::error('Для организации с oid: ' . $realOid
                                . ', при создании обращения (extId=' . $data['id'] . '), не могли создать связь объекта(uuid='
                                . $localObjectUuid . ') и контрагента (uuid=' . $contragent->uuid . ').',
                                self::LOG_TAG);
                            return false;
                        }
                    }
                }
            }

            // создаём запрос
            $request = new Request();
            $request->scenario = ZhkhActiveRecord::SCENARIO_API;
            $request->oid = $realOid;
            $request->uuid = MainFunctions::GUID();
            $request->type = 0; // 0 - бесплатная заявка (может можно сечь по категориям интерсвязи для точного определения)
            $request->contragentUuid = $contragent->uuid;
            $request->authorUuid = Users::USER_SERVICE_UUID; // sUser
            $request->requestStatusUuid = RequestStatus::NEW_REQUEST; // Состояние (1 - новое, 2 - в работе, 3 - закрыто)
            $requestType = RequestType::findOne(['title' => 'Другой характер обращения', 'oid' => $realOid])->uuid;
            $request->requestTypeUuid = $requestType;
            $request->comment = $data['category']['text'] . ': ' . $incident['description'];
            $request->verdict = '';
            $request->result = '';
            $request->equipmentUuid = null;
            $request->objectUuid = $localObjectUuid;
            $request->extId = $data['id'];
            $request->integrationClass = self::class;
//            $request->taskUuid; // null
            $request->closeDate = null; // ? current_timestamp можно ли установить в null?
            if (!$request->save()) {
                Yii::error('Для организации с oid: ' . $oid . ', не смогли создать обращение (extId=' . $data['id']
                    . ').', self::LOG_TAG);
                return false;
            }
        }

        return true;
    }

    /**
     * @param $oid string Uuid организации
     * @param $data array массив с инфорацией об обращении
     * @return bool
     * @throws Exception
     * @throws InvalidConfigException
     */
    private function updateAppeal($oid, $data)
    {
        // пока видно только три сущности которые меняются:
        // status(статус с ним всё ясно)
        // ownerUser(видимо ответственный, может быть и организацией и человеком) - решить
        // executorUser(исполнитель) - решить
        $statusUuid = null;
        switch ($data['status']['id']) {
            case self::IS_APPEAL_NEW:
                $statusUuid = RequestStatus::NEW_REQUEST;
                break;
            case self::IS_APPEAL_IN_WORK:
                $statusUuid = RequestStatus::IN_WORK;
                break;
            case self::IS_APPEAL_CLOSED:
                $statusUuid = RequestStatus::COMPLETE;
                break;
            default:
                Yii::error('Получен не известный статус к обращению(extId=' . $data['id']
                    . ', status=)' . $data['status']['text'], self::LOG_TAG);
                return false;
                break;
        }

        $req = Request::find()->where(['oid' => $oid, 'extId' => $data['id'], 'integrationClass' => self::class])->one();
        if ($req == null) {
            // возможно что по каким-то причинам нам не уведомила интерсвязь, мы не получили уведомление о новом обращении
            // нужно создать обращение по полученным данным
            if (!$this->createAppeal($oid, $data)) {
                Yii::error('Для организации с oid: ' . $oid . ', не смогли создать обращение (extId=' . $data['id']
                    . ').', self::LOG_TAG);
                return false;
            } else {
                return true;
            }
        }

        $req->scenario = Request::SCENARIO_API;
        $req->requestStatusUuid = $statusUuid;
        if (!$req->save()) {
            Yii::error('Для организации с oid: ' . $oid . ', не смогли изменить статус обращения (extId=' . $data['id']
                . ').', self::LOG_TAG);
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $data array массив с инфорацией об обращении
     * @return bool
     */
    private function createComment($data)
    {
        // ищем обращение с которым связан комментарий
        $request = Request::findOne(['extId' => $data['ticketId'], 'integrationClass' => self::class]);

        if ($request == null) {
            // коментарий к обращению которого у нас нет
            Yii::error('Получен новый коментарий к обращению(extId=' . $data['ticketId']
                . ') которого нет в системе.', self::LOG_TAG);
            return false;
        }

        $comment = new Comments();
        $comment->uuid = MainFunctions::GUID();
        $comment->oid = $request->oid;
        $comment->entityUuid = $request->uuid;
        $comment->text = $data['text'];
        $comment->extId = '' . $data['id'];
        $comment->extParentId = $request->extId;
        // для создания уникального индеска, все поля вхдящие в него должны быть заполнены
        // интерсвязь в $data['type'] передаёт всегда null
        $comment->extParentType = 'null';
        $comment->rawData = json_encode($data);
        $comment->date = date('Y-m-d H:i:s', strtotime($data['date']));
        $comment->integrationClass = self::class;
        if (!$comment->save()) {
            Yii::error('Для организации с oid: ' . $request->oid . ', не смогли сохранить новый комментарий. '
                . 'requestExtId=' . $data['ticketId'] . 'commentExtId=' . $data['id'], self::LOG_TAG);
            return false;
        }

        return true;
    }

    /**
     * @param $uuid string Uuid организации
     * @param $paramName string Названия параметра
     * @return Settings|null
     */
    private static function getOrgSetting($uuid, $paramName)
    {
        return Settings::findOne(['title' => $paramName . '-' . $uuid]);
    }

    /**
     * @param $oid string Uuid организации
     * @param $paramName string Название настройки
     * @param $data string Данные
     * @return bool
     */
    private static function setOrgSettings($oid, $paramName, $data)
    {
        $settings = Settings::findOne(['title' => $paramName . '-' . $oid]);
        if ($settings == null) {
            $settings = new Settings();
            $settings->uuid = MainFunctions::GUID();
            $settings->title = $paramName . '-' . $oid;
        }

        $settings->parameter = $data;

        if (!$settings->save()) {
            Yii::error('Для организации с oid: ' . $oid . ', не смогли сохранить настройку ' . $paramName, self::LOG_TAG);
            return false;
        }

        return true;
    }

    /**
     * @param $oid string Uuid организации
     * @param $appealId string Номер обращения во внешней системе
     * @param $text string Текст коментария
     * @return int
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function sendComment($oid, $appealId, $text)
    {
        $isApiSettings = self::getOrgSetting($oid, self::IS_API_PARAM_NAME);
        if ($isApiSettings == null) {
            Yii::error('Для организации с oid: ' . $oid . ', не найдены настройки API.', self::LOG_TAG);
            return -1;
        }

        $isApiSettings = json_decode($isApiSettings->parameter, true);

        $tokenJson = self::getToken($oid);
        if ($tokenJson == null) {
            Yii::error('Для организации с oid: ' . $oid . ', не получили токен.', self::LOG_TAG);
            return -1;
        }

        $tokenData = json_decode($tokenJson, true);

        $httpClient = new Client();
        $q = $isApiSettings['url'] . '/api/mc/appeals/' . $appealId . '/comments';
        /** @var \yii\httpclient\Response $response */
        $response = $httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($q)
            ->setHeaders([
                'from-user' => $tokenData['userId'],
                'Authorization' => $tokenData['tokenType'] . ' ' . $tokenData['token'],
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->setContent(json_encode([
                'text' => $text,
                'type' => null,
            ]))
            ->send();

        if ($response->isOk) {
            $answer = json_decode($response->content, true);
            return $answer['id'];
        } else {
            Yii::error('Для организации с oid: ' . $oid . ', не смогли отправить коментарий.', self::LOG_TAG);
            return -1;
        }
    }

    /**
     * @param $oid string Uuid организации
     * @param $appealId string Номер обращения во внешней системе
     * @param $text string Текст коментария
     * @return boolean
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function closeAppeal($oid, $appealId, $text)
    {
        $isApiSettings = self::getOrgSetting($oid, self::IS_API_PARAM_NAME);
        if ($isApiSettings == null) {
            Yii::error('Для организации с oid: ' . $oid . ', не найдены настройки API.', self::LOG_TAG);
            return false;
        }

        $isApiSettings = json_decode($isApiSettings->parameter, true);

        $tokenJson = self::getToken($oid);
        if ($tokenJson == null) {
            Yii::error('Для организации с oid: ' . $oid . ', не получили токен.', self::LOG_TAG);
            return false;
        }

        $tokenData = json_decode($tokenJson, true);

        $httpClient = new Client();
        $q = $isApiSettings['url'] . '/api/mc/appeals/' . $appealId;
        /** @var \yii\httpclient\Response $response */
        $response = $httpClient->createRequest()
            ->setMethod('PUT')
            ->setUrl($q)
            ->setHeaders([
                'from-user' => $tokenData['userId'],
                'Authorization' => $tokenData['tokenType'] . ' ' . $tokenData['token'],
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->setContent(json_encode([
                'comment' => $text,
                'statusId' => self::IS_APPEAL_CLOSED,
            ]))
            ->send();

        if ($response->isOk) {
            return true;
        } else {
            Yii::error('Для организации с oid: ' . $oid . ', не смогли закрыть обращение.', self::LOG_TAG);
            return false;
        }
    }


    /**
     * @param $oid
     * @return string|null
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private static function getToken($oid)
    {
        $tokenSettings = self::getOrgSetting($oid, self::IS_API_TOKEN_NAME);
        $tokenJson = null;
        if ($tokenSettings == null) {
            // запрашиваем токен
            $tokenJson = self::createToken($oid);
            if ($tokenJson == null) {
                return null;
            } else {
                // сохраняем токен
                if (!self::setOrgSettings($oid, self::IS_API_TOKEN_NAME, $tokenJson)) {
                    Yii::error('Для организации с oid: ' . $oid . ', не смогли сохранить токен.', self::LOG_TAG);
                }
            }
        } else {
            $tokenJson = $tokenSettings->parameter;
        }

        $tokenData = json_decode($tokenJson, true);

        // проверяем срок действия токена
        if (time() >= strtotime($tokenData['accessEnd'])) {
            // запрашиваем токен
            $tokenJson = self::createToken($oid);
            if ($tokenJson == null) {
                return null;
            } else {
                // сохраняем токен
                if (!self::setOrgSettings($oid, self::IS_API_TOKEN_NAME, $tokenJson)) {
                    Yii::error('Для организации с oid: ' . $oid . ', не смогли сохранить токен.', self::LOG_TAG);
                }
            }
        }

        return $tokenJson;
    }

    /**
     * @param $oid string Uuid организации
     * @return string|null json encoded token data
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private static function createToken($oid)
    {
        $isApiSettings = self::getOrgSetting($oid, self::IS_API_PARAM_NAME);
        $isApiSettings = json_decode($isApiSettings->parameter, true);

        $httpClient = new Client();
        $q = $isApiSettings['url'] . '/api/auth/password';
        /** @var \yii\httpclient\Response $response */
        $response = $httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($q)
            ->setHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->setContent(json_encode([
                'username' => $isApiSettings['user'],
                'password' => $isApiSettings['password'],
            ]))
            ->send();

        if ($response->isOk) {
            return $response->content;
        } else {
            Yii::error('Для организации с oid: ' . $oid . ', при запросе токена получили ответ ' . $response->statusCode, self::LOG_TAG);
            return null;
        }
    }
}
