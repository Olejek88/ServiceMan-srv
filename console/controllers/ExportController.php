<?php

namespace console\controllers;
use common\models\City;
use common\models\House;
use common\models\HouseType;
use common\models\Resident;
use common\models\Street;
use common\models\Subject;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use \yii\console\Controller;
use Yii;
use common\models\Flat;

class ExportController extends Controller
{
    const LOG_ID = 'export';

    public function actionLoadData()
    {
        echo('[' . self::LOG_ID . '] start load flats').PHP_EOL;
        echo('[' . self::LOG_ID . '] [' . Yii::$app->db->dsn . '] user/pass ' . Yii::$app->db->username).PHP_EOL;
        $reader = new Xls();
        for ($file_private = 1; $file_private <= 8; $file_private++) {
            $file_name = \Yii::$app->basePath."/export-data/data/1/0" . $file_private . ".2018.xls";
            echo('[' . self::LOG_ID . '] '.$file_name).PHP_EOL;
            $file = $reader->load($file_name);
            $sheet = $file->getActiveSheet();

            $cityFirst = City::find()->one();
            $houseTypePrivate = HouseType::find()->where(['title' => 'Частный дом'])->one();
            $houseTypeMKD = HouseType::find()->where(['title' => 'Многоквартирный дом'])->one();
            $houseTypeOther = HouseType::find()->where(['title' => 'Коммерческая организация'])->one();

            $houseStatus = '9236E1FF-D967-4080-9F42-59B03ADD25E8';
            $flatStatus = '9D86D530-1910-488E-87D9-FD2FE06CA5E7';

            $flatTypePrivate = '42686CFC-34D0-45FF-95A4-04B0D865EC35';
            $flatTypeInput = 'F68A562B-8F61-476F-A3E7-5666F9CEAFA1';

            $row_num=0;
            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                $cell_num=0;
                $city = '';
                $flat = '';
                $type = '';
                $house = '';
                $ls = '';
                $code ='';
                $street = '';
                foreach ($cellIterator as $cell) {
                    //echo '<td>' . $cell->getValue() . '</td>' . PHP_EOL;
                    switch ($cell_num) {
                        case 0: $type = $cell->getValue(); break;
                        case 1: $city = $cell->getValue(); break;
                        case 3: $street = $cell->getValue(); break;
                        case 4: $house = $cell->getValue(); break;
                        case 5: $flat = $cell->getValue(); break;
                        case 6: $ls = $cell->getValue(); break;
                        case 7: $code = $cell->getValue(); break;
                    }
                    $cell_num++;
                }
                if ($city=='Нязепетровск' || $city=='МКД') {
                    $flatType = $flatTypePrivate;
                    $houseType = $houseTypeOther['uuid'];
                    if ($flat=='' || $flat==null)
                        $flatType = $flatTypeInput;
                    if ($type=='1')
                        $houseType = $houseTypePrivate['uuid'];
                    if ($type=='3')
                        $houseType = $houseTypeMKD['uuid'];
                    $ls_code = $ls.' ['.$code.']';
                    $this->StoreHouse($houseType, $street, $house, $flat, $ls_code, $cityFirst,
                        $houseStatus, $flatStatus, $flatType);
                }
                $row_num++;
            }
        }
    }

    public function actionLoadSubject()
    {
        echo('[' . self::LOG_ID . '] start load subjects').PHP_EOL;
        echo('[' . self::LOG_ID . '] [' . Yii::$app->db->dsn . '] user/pass ' . Yii::$app->db->username).PHP_EOL;
        $reader = new Xls();
        $file_name = \Yii::$app->basePath."/export-data/data/2018.xls";
        echo('[' . self::LOG_ID . '] '.$file_name).PHP_EOL;
        $file = $reader->load($file_name);
        $sheet = $file->getActiveSheet();

        $cityFirst = City::find()->one();
        $houseTypeSchool = HouseType::find()->where(['title' => 'Школа'])->one();
        $houseTypeMDOU = HouseType::find()->where(['title' => 'Детский сад'])->one();
        $houseTypeCommercial = HouseType::find()->where(['title' => 'Коммерческая организация'])->one();
        $houseTypeBudget = HouseType::find()->where(['title' => 'Бюджетное учереждение'])->one();
        $houseTypeOther = HouseType::find()->where(['title' => 'Другой'])->one();

        $houseStatus = '9236E1FF-D967-4080-9F42-59B03ADD25E8';
        $flatStatus = '9D86D530-1910-488E-87D9-FD2FE06CA5E7';
        $flatTypeInput = 'F68A562B-8F61-476F-A3E7-5666F9CEAFA1';

        $row_num = 0;
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $cell_num = 0;
            $title = '';
            $dogovor = '';
            foreach ($cellIterator as $cell) {
                switch ($cell_num) {
                    case 0:
                        $title = $cell->getValue();
                        break;
                    case 1:
                        $dogovor = $cell->getValue();
                        break;
                }
                $cell_num++;
            }
            if ($dogovor != '') {
                $houseType = $houseTypeOther['uuid'];
                if (strstr($title,'ИП') || strstr($title,'ООО') || strstr($title,'ФЛ') ||
                    strstr($title,'АО') || strstr($title,'ОАО'))
                    $houseType = $houseTypeCommercial;
                if (strstr($title,'МКДОУ'))
                    $houseType = $houseTypeMDOU;
                if (strstr($title,'школа'))
                    $houseType = $houseTypeSchool;
                $flatValue = 'Котельная';
                $street = 'nodd';
                $this->StoreHouse($title, $dogovor, $street, $house, $cityFirst, $flatValue, $houseStatus, $houseType,
                    $flatStatus, $flatType);
            }
            $row_num++;
        }
    }

    private function StoreHouse ($title, $dogovor, $streetValue, $houseValue, $cityFirst, $flatValue, $houseStatus,
                                 $houseType, $flatStatus, $flatType) {
        $street = Street::find()->where(['title' => $streetValue])->one();
        if ($street==null && $cityFirst!=null) {
            $street = new Street();
            $street->uuid = self::GUID();
            $street->cityUuid = $cityFirst->uuid;
            $street->title = $streetValue;
            $street->changedAt = new Date();
            $street->createdAt = new Date();
            echo ('store street: '.$street->title.' ['.$street->uuid.']'.PHP_EOL);
            $street->save();
        }
        $house = House::find()->where(['number' => $houseValue])->andWhere(['streetUuid' => $street->uuid])->one();
        if ($house==null) {
            $house = new House();
            $house->uuid = self::GUID();
            $house->streetUuid = $street->uuid;
            $house->number = $houseValue;
            $house->houseStatusUuid=$houseStatus;
            $house->houseTypeUuid=$houseType;
            $house->changedAt = new Date();
            $house->createdAt = new Date();
            echo ('store house: '.$house->number.' ['.$house->uuid.']'.PHP_EOL);
            $house->save();
        }

        if ($flatValue=='' || $flatValue==null) $flatValue = "Котельная";
        $flat = Flat::find()->where(['number' => $flatValue])->andWhere(['houseUuid' => $house->uuid])->one();
        if ($flat == null) {
            $flat = new Flat();
            $flat->uuid = self::GUID();
            $flat->houseUuid = $house->uuid;
            $flat->number = $flatValue;
            $flat->flatStatusUuid = $flatStatus;
            $flat->flatTypeUuid = $flatType;
            $flat->changedAt = new Date();
            $flat->createdAt = new Date();
            echo ('store flat: '.$flat->number.' ['.$flat->uuid.']'.PHP_EOL);
            $flat->save();
        }

        $subject = Subject::find()->where(['contractNumber' => $dogovor])->andWhere(['houseUuid' => $house->uuid])->one();
        if ($resident == null) {
            $resident = new Subject();
            $resident->uuid = self::GUID();
            $resident->houseUuid = $house->uuid;
            $resident->contractDate = new Date();
            $resident->contractNumber = $dogovor;
            $resident->changedAt = new Date();
            $resident->createdAt = new Date();
            echo ('store resident: '.$resident->inn.' ['.$resident->uuid.']'.PHP_EOL);
            $resident->save();
        }
    }
    
        public static function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

}