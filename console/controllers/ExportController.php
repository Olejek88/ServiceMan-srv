<?php

namespace console\controllers;

use common\components\MainFunctions;
use common\models\City;
use common\models\Equipment;
use common\models\Flat;
use common\models\House;
use common\models\HouseType;
use common\models\Measure;
use common\models\Message;
use common\models\PhotoEquipment;
use common\models\PhotoFlat;
use common\models\Resident;
use common\models\Street;
use common\models\Subject;
use common\models\UserHouse;
use common\models\Users;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Yii;
use yii\console\Controller;

class ExportController extends Controller
{
    const LOG_ID = 'export';

    public function actionAddData()
    {
        echo ('[' . self::LOG_ID . '] start add new object') . PHP_EOL;
        echo ('[' . self::LOG_ID . '] [' . Yii::$app->db->dsn . '] user/pass ' . Yii::$app->db->username) . PHP_EOL;
        $reader = new Xls();
        $file_name = \Yii::$app->basePath . "/export-data/data/new_objects.xls";
        echo ('[' . self::LOG_ID . '] ' . $file_name) . PHP_EOL;
        $file = $reader->load($file_name);
        $sheet = $file->getActiveSheet();

        $cityFirst = City::find()->one();
        $houseStatus = '9127B1A3-D0C1-4F96-8026-B597600FC9CD';
        $flatStatus = '9D86D530-1910-488E-87D9-FD2FE06CA5E7';
        $flatTypeInput = 'F68A562B-8F61-476F-A3E7-5666F9CEAFA1';

        $houseTypeSchool = HouseType::find()->where(['title' => 'Школа'])->one();
        $houseTypeMDOU = HouseType::find()->where(['title' => 'Детский сад'])->one();
        $houseTypeCommercial = HouseType::find()->where(['title' => 'Коммерческая организация'])->one();
        $houseTypeBudget = HouseType::find()->where(['title' => 'Бюджетное учереждение'])->one();
        $houseTypeOther = HouseType::find()->where(['title' => 'Другой'])->one();
        $houseTypePrivate = HouseType::find()->where(['title' => 'Частный дом'])->one();
        $houseTypeMKD = HouseType::find()->where(['title' => 'Многоквартирный дом'])->one();

        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $cell_num = 0;
            $userValue = '';
            $streetValue = '';
            $flat = '';
            $type = 1;
            $house = '';
            $houseType = '';
            $subject = '';

            foreach ($cellIterator as $cell) {
                switch ($cell_num) {
                    case 0:
                        $userValue = $cell->getValue();
                        break;
                    case 1:
                        $streetValue = $cell->getValue();
                        break;
                    case 2:
                        $house = ''.$cell->getValue();
                        break;
                    case 3:
                        $flat = ''.$cell->getValue();
                        break;
                    case 4:
                        $houseTypeValue = $cell->getValue();
                        $houseType = $houseTypeOther['uuid'];
                        if ($houseTypeValue == 'Коммерческий')
                            $houseType = $houseTypeCommercial['uuid'];
                        if ($houseTypeValue == 'МДОУ')
                            $houseType = $houseTypeMDOU['uuid'];
                        if ($houseTypeValue == 'Школа')
                            $houseType = $houseTypeSchool['uuid'];
                        if ($houseTypeValue == 'Бюджет')
                            $houseType = $houseTypeBudget['uuid'];
                        if ($houseTypeValue == 'МКД')
                            $houseType = $houseTypeMKD['uuid'];
                        if ($houseTypeValue == 'Частный')
                            $houseType = $houseTypePrivate['uuid'];
                        break;
                    case 5:
                        $subject = $cell->getValue();
                        if ($subject!='')
                            $type=2;
                        else
                            $type=1;
                        break;
                }
                $cell_num++;
            }
            echo $streetValue .' '. $userValue.PHP_EOL;
            if ($streetValue != '' && $userValue != '') {
                if ($flat=='') $flat="Вводной";
                echo 'Store2House'.PHP_EOL;
                $this->Store2House($type, $subject, $streetValue, $house, $cityFirst, $flat, $houseStatus,
                                             $houseType, $flatStatus, $flatTypeInput, $userValue);
            }
        }
    }

    public function actionLoadData()
    {
        echo ('[' . self::LOG_ID . '] start load flats') . PHP_EOL;
        echo ('[' . self::LOG_ID . '] [' . Yii::$app->db->dsn . '] user/pass ' . Yii::$app->db->username) . PHP_EOL;
        $reader = new Xls();
        for ($year = 2018; $year <= 2018; $year++) {
            for ($file_private = 9; $file_private <= 12; $file_private++) {
                $file_name = \Yii::$app->basePath . "/export-data/data/" . $year . "/" . sprintf("%02d", $file_private) . "." . $year . ".xls";
                echo ('[' . self::LOG_ID . '] ' . $file_name) . PHP_EOL;
                if (file_exists($file_name)) {
                    $file = $reader->load($file_name);
                    $sheet = $file->getActiveSheet();

                    $cityFirst = City::find()->one();
                    $houseTypePrivate = HouseType::find()->where(['title' => 'Частный дом'])->one();
                    $houseTypeMKD = HouseType::find()->where(['title' => 'Многоквартирный дом'])->one();
                    $houseTypeOther = HouseType::find()->where(['title' => 'Коммерческая организация'])->one();

                    $houseStatus = '9127B1A3-D0C1-4F96-8026-B597600FC9CD';
                    $flatStatus = '9D86D530-1910-488E-87D9-FD2FE06CA5E7';

                    $flatTypePrivate = '42686CFC-34D0-45FF-95A4-04B0D865EC35';
                    $flatTypeInput = 'F68A562B-8F61-476F-A3E7-5666F9CEAFA1';

                    $row_num = 0;
                    foreach ($sheet->getRowIterator() as $row) {
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(FALSE);
                        $cell_num = 0;
                        $city = '';
                        $flat = '';
                        $type = '';
                        $house = '';
                        $ls = '';
                        $code = '';
                        $street = '';
                        foreach ($cellIterator as $cell) {
                            //echo '<td>' . $cell->getValue() . '</td>' . PHP_EOL;
                            switch ($cell_num) {
                                case 0:
                                    $type = $cell->getValue();
                                    break;
                                case 1:
                                    $city = $cell->getValue();
                                    break;
                                case 3:
                                    $street = $cell->getValue();
                                    break;
                                case 4:
                                    $house = $cell->getValue();
                                    break;
                                case 5:
                                    $flat = $cell->getValue();
                                    break;
                                case 6:
                                    $ls = $cell->getValue();
                                    break;
                                case 7:
                                    $code = $cell->getValue();
                                    break;
                            }
                            $cell_num++;
                        }
                        if ($city == 'Нязепетровск' || $city == 'МКД') {
                            $flatType = $flatTypePrivate;
                            $houseType = $houseTypeOther['uuid'];
                            if ($flat == '' || $flat == null)
                                $flatType = $flatTypeInput;
                            if ($type == '1')
                                $houseType = $houseTypePrivate['uuid'];
                            if ($type == '3')
                                $houseType = $houseTypeMKD['uuid'];
                            $ls_code = $ls . ' [' . $code . ']';
                            $this->StoreHouse(1, "", $ls_code, $street, $house, $cityFirst, $flat,
                                $houseStatus, $houseType, $flatStatus, $flatType);
                        }
                        $row_num++;
                    }
                }
            }
        }
    }

    public function actionUserHouse()
    {
        echo ('[' . self::LOG_ID . '] start user house definition') . PHP_EOL;
        $reader = new Xls();
        $file_name = \Yii::$app->basePath . "/export-data/data/controller.xls";
        echo ('[' . self::LOG_ID . '] ' . $file_name) . PHP_EOL;
        $file = $reader->load($file_name);
        $sheet = $file->getActiveSheet();
        $count = 1;
        $row_num = 0;
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $cell_num = 0;
            $userValue = '';
            $streetValue = '';
            foreach ($cellIterator as $cell) {
                switch ($cell_num) {
                    case 1:
                        $streetValue = $cell->getValue() . "";
                        break;
                    case 2:
                        $userValue = $cell->getValue() . "";
                        break;
                }
                $cell_num++;
            }
            if ($streetValue != '' && $userValue != '') {
                $user = Users::find()->where(['_id' => $userValue])->one();
                $street = Street::find()->where(['title' => $streetValue])->one();
                if ($street != null && $user != null) {
                    $houses = House::find()->where(['streetUuid' => $street['uuid']])->all();
                    foreach ($houses as $house) {
                        $userHouse = UserHouse::find()->where(['houseUuid' => $house['uuid']])->one();
                        if ($userHouse == null) {
/*                            $userHouse = new UserHouse();
                            $userHouse->uuid = MainFunctions::GUID();
                            $userHouse->userUuid = $user['uuid'];
                            $userHouse->houseUuid = $house['uuid'];
                            $userHouse->changedAt = date('Y-m-d H:i:s');
                            $userHouse->createdAt = date('Y-m-d H:i:s');
                            echo('store user house: ' . $street['title'] . ',' . $house['number'] . ' [' . $user['name'] . ']' . PHP_EOL);*/
                            //$userHouse->save();
                        }
                        else {
                            $userHouse->userUuid = $user['uuid'];
                            echo('['.$count.'] update user house: ' . $street['title'] . ',' . $house['number'] . ' [' . $user['name'] . ']' . PHP_EOL);
                            $count++;
                            $userHouse->save();
                        }
                    }
                }
            } else {
                echo 'cannot find street=' . $streetValue . ' | user=' . $userValue;
            }
            $row_num++;
        }
    }

    public function actionUserRe()
    {
        echo ('[' . self::LOG_ID . '] start user house redefinition') . PHP_EOL;
        $count = 1;
        $user2 = Users::find()->where(['_id' => 2])->one();
        $user7 = Users::find()->where(['_id' => 7])->one();
        $user17 = Users::find()->where(['_id' => 17])->one();
        $user8 = Users::find()->where(['_id' => 8])->one();
        $user9 = Users::find()->where(['_id' => 9])->one();
        $user10 = Users::find()->where(['_id' => 10])->one();
        $user11 = Users::find()->where(['_id' => 11])->one();
        $user12 = Users::find()->where(['_id' => 12])->one();
        $user13 = Users::find()->where(['_id' => 13])->one();
        $userHouses = UserHouse::find()->all();
        foreach ($userHouses as $userHouse) {
            if ($userHouse != null) {
                if ($userHouse['userUuid'] == $user8)
                    $userHouse['userUuid'] = $user2['uuid'];
                if ($userHouse['userUuid'] == $user9)
                    $userHouse['userUuid'] = $user2['uuid'];

                if ($userHouse['userUuid'] == $user10)
                    $userHouse['userUuid'] = $user7['uuid'];
                if ($userHouse['userUuid'] == $user13)
                    $userHouse['userUuid'] = $user7['uuid'];

                if ($userHouse['userUuid'] == $user11)
                    $userHouse['userUuid'] = $user17['uuid'];
                if ($userHouse['userUuid'] == $user12)
                    $userHouse['userUuid'] = $user17['uuid'];

                if ($userHouse['userUuid'] == $user8 ||
                    $userHouse['userUuid'] == $user9 ||
                    $userHouse['userUuid'] == $user10 ||
                    $userHouse['userUuid'] == $user11 ||
                    $userHouse['userUuid'] == $user12 ||
                    $userHouse['userUuid'] == $user13) {
                    $userHouse['userUuid'] = $user2['uuid'];
                    echo('[' . $count . '] update user house: ' . $userHouse['uuid'] . ' [' . $userHouse['userUuid'] . ']' . PHP_EOL);
                    $count++;
                    //$userHouse->save();
                }
            }
        }
    }

    public function actionUserLeft()
    {
        echo ('[' . self::LOG_ID . '] start user house delete') . PHP_EOL;
        echo ('[' . self::LOG_ID . '] [' . Yii::$app->db->dsn . '] user/pass ' . Yii::$app->db->username) . PHP_EOL;
        $reader = new Xls();
        for ($year = 2016; $year <= 2018; $year++) {
            for ($file_private = 1; $file_private <= 12; $file_private++) {
                $file_name = \Yii::$app->basePath . "/export-data/data/" . $year . "/" . sprintf("%02d", $file_private) . "." . $year . ".xls";
                echo ('[' . self::LOG_ID . '] ' . $file_name) . PHP_EOL;
                if (file_exists($file_name)) {
                    $file = $reader->load($file_name);
                    $sheet = $file->getActiveSheet();
                    $row_num = 0;
                    foreach ($sheet->getRowIterator() as $row) {
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(FALSE);
                        $cell_num = 0;
                        $city = '';
                        $flat = '';
                        $house = '';
                        $street = '';
                        $name = '';
                        foreach ($cellIterator as $cell) {
                            switch ($cell_num) {
                                case 1:
                                    $city = $cell->getValue();
                                    break;
                                case 3:
                                    $street = $cell->getValue();
                                    break;
                                case 4:
                                    $house = $cell->getValue();
                                    break;
                                case 5:
                                    $flat = $cell->getValue();
                                    break;
                                case 8:
                                    $name = $cell->getValue();
                                    break;
                            }
                            $cell_num++;
                        }
                        if ($name == 'Летний водопровод' || $city == 'Водоснабжение из скважин')
                            if ($flat == '') {
                                $street = Street::find()->where(['title' => $street])->one();
                                if ($street != null) {
                                    $house = House::find()->where(['streetUuid' => $street['uuid']])
                                        ->andWhere(['number' => $house])->one();
                                    if ($house != null) {
                                        $userHouse = UserHouse::find()->where(['houseUuid' => $house['uuid']])->one();
                                        if ($userHouse != null) {
                                            $userHouse->delete();
                                            echo('deassign user house: ' . $street['title'] . ',' . $house['number'] . ' [' . $userHouse['uuid'] . ']' . PHP_EOL);
                                        }
                                    }
                                }
                            }
                        $row_num++;
                    }
                }
            }
        }
    }

    public function actionDeleteFlat()
    {
        echo ('[' . self::LOG_ID . '] start equipment and flat delete') . PHP_EOL;
        echo ('[' . self::LOG_ID . '] [' . Yii::$app->db->dsn . '] user/pass ' . Yii::$app->db->username) . PHP_EOL;
        $reader = new Xls();
        $file_name = \Yii::$app->basePath . "/export-data/data/flat_delete.xls";
        echo ('[' . self::LOG_ID . '] ' . $file_name) . PHP_EOL;
        $file = $reader->load($file_name);
        $sheet = $file->getActiveSheet();
        $flatStatus = '9D86D530-1910-488E-87D9-FD2FE06CA5E7';
        $flatTypeInput = 'F68A562B-8F61-476F-A3E7-5666F9CEAFA1';

        $row_num = 0;
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $cell_num = 0;
            $homeValue = '';
            $streetValue = '';
            foreach ($cellIterator as $cell) {
                switch ($cell_num) {
                    case 0:
                        $streetValue = $cell->getValue() . "";
                        break;
                    case 1:
                        $homeValue = $cell->getValue() . "";
                        break;
                }
                $cell_num++;
            }
            if ($streetValue != '' && $homeValue != '') {
                $street = Street::find()->where(['title' => $streetValue])->one();
                if ($street != null) {
                    $house = House::find()->where(['streetUuid' => $street['uuid']])->andWhere(['number' => $homeValue])->one();
                    if ($house != null) {
                        $flats = Flat::find()->where(['houseUuid' => $house['uuid']])->all();
                        $main_counter = 0;
                        foreach ($flats as $flat) {
                            if (strlen($flat['number']) < 4) {
                                echo($street['title'] . ',' . $house['number'] . ', ' . $flat['number'] . PHP_EOL);

                                $messages = Message::find()->where(['flatUuid' => $flat['uuid']])->all();
                                foreach ($messages as $message) {
                                    echo('delete message: [' . $message['uuid'] . ']' . PHP_EOL);
                                    $message->delete();
                                }

                                $photos = PhotoFlat::find()->where(['flatUuid' => $flat['uuid']])->all();
                                foreach ($photos as $photo) {
                                    echo('delete photo: [' . $photo['uuid'] . ']' . PHP_EOL);
                                    $photo->delete();
                                }

                                $equipments = Equipment::find()->where(['flatUuid' => $flat['uuid']])->all();
                                foreach ($equipments as $equipment) {
                                    $measures = Measure::find()->where(['equipmentUuid' => $equipment['uuid']])->all();
                                    foreach ($measures as $measure) {
                                        echo('delete measure: [' . $measure['uuid'] . ']' . PHP_EOL);
                                        $measure->delete();
                                    }
                                    $photos = PhotoEquipment::find()->where(['equipmentUuid' => $equipment['uuid']])->all();
                                    foreach ($photos as $photo) {
                                        echo('delete photo: [' . $photo['uuid'] . ']' . PHP_EOL);
                                        $photo->delete();
                                    }
                                    echo('delete equipment: ' . $street['title'] . ',' . $house['number'] . ' [' . $equipment['uuid'] . ']' . PHP_EOL);
                                    $equipment->delete();
                                }
                                $residents = Resident::find()->where(['flatUuid' => $flat['uuid']])->all();
                                foreach ($residents as $resident) {
                                    echo('delete resident: [' . $resident['uuid'] . ']' . PHP_EOL);
                                    $resident->delete();
                                }
                                echo('delete flat: ' . $street['title'] . ',' . $house['number'] . ' [' . $flat['number'] . ']' . PHP_EOL);
                                $flat->delete();
                            } else $main_counter = 1;
                        }
                        if ($main_counter == 0) {
                            $flatValue = "Котельная";
                            $flat = new Flat();
                            $flat->uuid = MainFunctions::GUID();
                            $flat->houseUuid = $house['uuid'];
                            $flat->number = $flatValue;
                            $flat->flatStatusUuid = $flatStatus;
                            $flat->flatTypeUuid = $flatTypeInput;
                            $flat->changedAt = date('Y-m-d H:i:s');
                            $flat->createdAt = date('Y-m-d H:i:s');
                            echo('store flat: ' . $flat->number . ' [' . $flat->uuid . ']' . PHP_EOL);
                            $flat->save();
                        }
                    }
                }
            } else {
                echo 'cannot find street=' . $streetValue . ' | user=' . $homeValue;
            }
            $row_num++;
        }
    }

    public function actionLoadSubject()
    {
        echo ('[' . self::LOG_ID . '] start load subjects') . PHP_EOL;
        echo ('[' . self::LOG_ID . '] [' . Yii::$app->db->dsn . '] user/pass ' . Yii::$app->db->username) . PHP_EOL;
        $reader = new Xls();
        $file_name = \Yii::$app->basePath . "/export-data/data/2018.xls";
        echo ('[' . self::LOG_ID . '] ' . $file_name) . PHP_EOL;
        $file = $reader->load($file_name);
        $sheet = $file->getActiveSheet();

        $cityFirst = City::find()->one();
        $houseTypeSchool = HouseType::find()->where(['title' => 'Школа'])->one();
        $houseTypeMDOU = HouseType::find()->where(['title' => 'Детский сад'])->one();
        $houseTypeCommercial = HouseType::find()->where(['title' => 'Коммерческая организация'])->one();
        $houseTypeBudget = HouseType::find()->where(['title' => 'Бюджетное учереждение'])->one();
        $houseTypeOther = HouseType::find()->where(['title' => 'Другой'])->one();

        $houseStatus = '9127B1A3-D0C1-4F96-8026-B597600FC9CD';
        $flatStatus = '9D86D530-1910-488E-87D9-FD2FE06CA5E7';
        $flatTypeInput = 'F68A562B-8F61-476F-A3E7-5666F9CEAFA1';

        $row_num = 0;
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $cell_num = 0;
            $title = '';
            $dogovor = '';
            $type = '';
            $street = '';
            $house = '';
            foreach ($cellIterator as $cell) {
                switch ($cell_num) {
                    case 0:
                        $title = $cell->getValue();
                        break;
                    case 1:
                        $adr = $cell->getValue();
                        if ($adr != null) {
                            $address = str_replace("ул.", "", $adr);
                            $address = trim($address);
                            $pieces = explode(",", $address);
                            //echo $pieces[0];
                            if (count($pieces) == 2) {
                                $street = trim($pieces[0]);
                                $house = trim($pieces[1]);
                            }
                            //ул. Бархатовой,15
                        }
                        break;
                    case 2:
                        $dogovor = $cell->getValue() . "";
                        break;
                    case 3:
                        $type = $cell->getValue();
                        break;
                }
                $cell_num++;
            }
            if ($dogovor != '' && $street != '' && $house != '') {
                $houseType = $houseTypeOther['uuid'];
                if ($type == '11')
                    $houseType = $houseTypeCommercial['uuid'];
                if ($type == '10')
                    $houseType = $houseTypeMDOU['uuid'];
                if ($type == '9')
                    $houseType = $houseTypeSchool['uuid'];
                if ($type == '13')
                    $houseType = $houseTypeBudget['uuid'];
                $flatValue = "Вводной №" . $dogovor;
                $this->StoreHouse(2, $title, $dogovor, $street, $house, $cityFirst, $flatValue, $houseStatus, $houseType,
                    $flatStatus, $flatTypeInput);
            }
            $row_num++;
        }
    }

    private function StoreHouse($type, $title, $dogovor, $streetValue, $houseValue, $cityFirst, $flatValue, $houseStatus,
                                $houseType, $flatStatus, $flatType)
    {
        $street = Street::find()->where(['title' => $streetValue])->one();
        if ($street == null && $cityFirst != null) {
            $street = new Street();
            $street->uuid = MainFunctions::GUID();
            $street->cityUuid = $cityFirst->uuid;
            $street->title = $streetValue;
            $street->changedAt = date('Y-m-d H:i:s');
            $street->createdAt = date('Y-m-d H:i:s');
            echo('store street: ' . $street->title . ' [' . $street->uuid . ']' . PHP_EOL);
            $street->save();
        }
        $house = House::find()->where(['number' => $houseValue])->andWhere(['streetUuid' => $street->uuid])->one();
        if ($house == null) {
            $house = new House();
            $house->uuid = MainFunctions::GUID();
            $house->streetUuid = $street->uuid;
            $house->number = $houseValue;
            $house->houseStatusUuid = $houseStatus;
            $house->houseTypeUuid = $houseType;
            $house->changedAt = date('Y-m-d H:i:s');
            $house->createdAt = date('Y-m-d H:i:s');
            echo('store house: ' . $street->title . ',' . $house->number . ' [' . $house->uuid . ']' . PHP_EOL);
            $house->save();
        }

        if ($flatValue == '' || $flatValue == null) $flatValue = "Котельная";
        $flat = Flat::find()->where(['number' => $flatValue])->andWhere(['houseUuid' => $house->uuid])->one();
        if ($flat == null) {
            $flat = new Flat();
            $flat->uuid = MainFunctions::GUID();
            $flat->houseUuid = $house->uuid;
            $flat->number = $flatValue;
            $flat->flatStatusUuid = $flatStatus;
            $flat->flatTypeUuid = $flatType;
            $flat->changedAt = date('Y-m-d H:i:s');
            $flat->createdAt = date('Y-m-d H:i:s');
            echo('store flat: ' . $flat->number . ' [' . $flat->uuid . ']' . PHP_EOL);
            $flat->save();
        }

        if ($type == 1) {
            $resident = Resident::find()->where(['inn' => $dogovor])->andWhere(['flatUuid' => $flat->uuid])->one();
            if ($resident == null) {
                $resident = new Resident();
                $resident->uuid = MainFunctions::GUID();
                $resident->flatUuid = $flat->uuid;
                $resident->owner = "Ф.И.О.";
                $resident->inn = $dogovor;
                $resident->changedAt = date('Y-m-d H:i:s');
                $resident->createdAt = date('Y-m-d H:i:s');
                echo('store resident: ' . $resident->owner . ' [' . $resident->uuid . ']' . PHP_EOL);
                $resident->save();
            }
        } else {
            $subject = Subject::find()->where(['contractNumber' => $dogovor])->one();
            if ($subject == null) {
                $subject = new Subject();
                $subject->uuid = MainFunctions::GUID();
                $subject->owner = $title;
                $subject->flatUuid = $flat->uuid;
                $subject->houseUuid = $house->uuid;
                $subject->contractDate = date('Y-m-d H:i:s');
                $subject->contractNumber = $dogovor;
                $subject->changedAt = date('Y-m-d H:i:s');
                $subject->createdAt = date('Y-m-d H:i:s');
                echo('store subject: ' . $subject->owner . ' ' . $subject->contractNumber . ' [' . $subject->uuid . ']' . PHP_EOL);
                $subject->save();
            }
        }
    }

    private function Store2House($type, $title, $streetValue, $houseValue, $cityFirst, $flatValue, $houseStatus,
                                 $houseType, $flatStatus, $flatType, $userValue)
    {
        $street = Street::find()->where(['title' => $streetValue])->one();
        if ($street != null && $cityFirst != null) {
            $house = House::find()->where(['number' => $houseValue])->andWhere(['streetUuid' => $street['uuid']])->one();
            if ($house == null) {
                $house = new House();
                $house->uuid = MainFunctions::GUID();
                $house->streetUuid = $street['uuid'];
                $house->number = $houseValue;
                $house->houseStatusUuid = $houseStatus;
                $house->houseTypeUuid = $houseType;
                $house->changedAt = date('Y-m-d H:i:s');
                $house->createdAt = date('Y-m-d H:i:s');
                echo('store house: ' . $street['uuid'].' '.$street['title'] . ',' . $house->number . ' [' . $house->uuid . ']' . PHP_EOL);
                $house->save();
                //echo json_encode($house->errors);
            }

            if ($flatValue == '' || $flatValue == null) $flatValue = "Котельная";
            $flat = Flat::find()->where(['number' => $flatValue])->andWhere(['houseUuid' => $house->uuid])->one();
            if ($flat == null) {
                $flat = new Flat();
                $flat->uuid = MainFunctions::GUID();
                $flat->houseUuid = $house->uuid;
                $flat->number = $flatValue;
                $flat->flatStatusUuid = $flatStatus;
                $flat->flatTypeUuid = $flatType;
                $flat->changedAt = date('Y-m-d H:i:s');
                $flat->createdAt = date('Y-m-d H:i:s');
                echo('store flat: ' . $flat->number . ' [' . $flat->uuid . ']' . PHP_EOL);
                $flat->save();
            }

            if ($type == 1) {
                $resident = new Resident();
                $resident->uuid = MainFunctions::GUID();
                $resident->flatUuid = $flat->uuid;
                $resident->owner = "Ф.И.О.";
                $resident->inn = '111'.random_int(1000000, 9999999);
                $resident->changedAt = date('Y-m-d H:i:s');
                $resident->createdAt = date('Y-m-d H:i:s');
                echo('store resident: ' . $resident->owner . ' [' . $resident->uuid . ']' . PHP_EOL);
                $resident->save();
            } else {
                $subject = new Subject();
                $subject->uuid = MainFunctions::GUID();
                $subject->owner = $title;
                $subject->flatUuid = $flat->uuid;
                $subject->houseUuid = $house->uuid;
                $subject->contractDate = date('Y-m-d H:i:s');
                $subject->contractNumber = '111'.random_int(1000000, 9999999);
                $subject->changedAt = date('Y-m-d H:i:s');
                $subject->createdAt = date('Y-m-d H:i:s');
                echo('store subject: ' . $subject->owner . ' ' . $subject->contractNumber . ' [' . $subject->uuid . ']' . PHP_EOL);
                $subject->save();
            }

            $user = Users::find()->where(['name' => $userValue])->one();
            if ($user!=null) {
                $userHouse = UserHouse::find()->where(['userUuid' => $user['uuid']])->andWhere(['houseUuid' => $house['uuid']])->one();
                if ($userHouse == null) {
                    $userHouse = new UserHouse();
                    $userHouse->uuid = MainFunctions::GUID();
                    $userHouse->userUuid = $user['uuid'];
                    $userHouse->houseUuid = $house['uuid'];
                    $userHouse->changedAt = date('Y-m-d H:i:s');
                    $userHouse->createdAt = date('Y-m-d H:i:s');
                    echo('store user house: ' . $street['title'] . ',' . $house['number'] . ' [' . $user['name'] . ']' . PHP_EOL);
                    $userHouse->save();
                }
            }
        }
    }
}