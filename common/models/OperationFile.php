<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\models;

use common\components\MyHelpers;
use common\modules\selectdb\Module;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "operation_file".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $operationUuid
 * @property string $fileName
 * @property string $createdAt
 * @property string $changedAt
 */
class OperationFile extends ActiveRecord
{
    private static $_IMAGE_ROOT = 'photo';

    /**
     * Название таблицы.
     *
     * @return string
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operation_file';
    }

    /**
     * Rules.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'operationUuid', 'fileName'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'operationUuid'], 'string', 'max' => 50],
            [['fileName'], 'string', 'max' => 100],
        ];
    }

    /**
     * Labels.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'operationUuid' => Yii::t('app', 'Uuid Операции'),
            'fileName' => Yii::t('app', 'Имя файла'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * URL изображения.
     *
     * @return string
     */
    public function getImageUrl()
    {
        $dbName = \Yii::$app->session->get('user.dbname');
        $typeUuid = $this->operation->taskStage->equipment->uuid;
        $localPath = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/' . $this->fileName;
        if (file_exists(Yii::getAlias($localPath))) {
            $userName = \Yii::$app->user->identity->username;
            $dir = 'storage/' . $userName . '/' . self::$_IMAGE_ROOT . '/'
                . $typeUuid . '/' . $this->fileName;
            $url = Yii::$app->request->BaseUrl . '/' . $dir;
        } else {
            $url = null;
        }

        return $url;
    }

    /**
     * Возвращает каталог в котором должен находится файл изображения,
     * относительно папки web.
     *
     * @return string
     */
    public function getImageDir()
    {
        $typeUuid = $this->operation->taskStage->equipment->uuid;
        $dbName = Module::chooseByToken(Yii::$app->request);
        $dir = 'storage/' . $dbName . '/' . self::$_IMAGE_ROOT . '/'
            . $typeUuid . '/';
        return $dir;
    }

    /**
     * Вспомогательный метод для сохранения загруженного
     * файла и создания записи о нём.
     *
     * @param string $file Имя файла для записи.
     *
     * @return OperationFile | null
     */
    public static function saveUploadFile($file)
    {
        // создаём запись в базе о файле
        $operationFile = OperationFile::findOne(
            ['_id' => $file['_id'], 'uuid' => $file['uuid']]
        );
        if ($operationFile == null) {
            $operationFile = new OperationFile();
        }

        $operationFile->attributes = $file;
        $operationFile->_id = $file['_id'];
        $operationFile->createdAt = MyHelpers::parseFormatDate($file['createdAt']);
        $operationFile->changedAt = MyHelpers::parseFormatDate($file['changedAt']);

        $dir = Yii::getAlias('@backend/web/');
        $dir = $dir . $operationFile->getImageDir() . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileMoved = move_uploaded_file(
            $_FILES['file']['tmp_name'][$file['uuid']], $dir . $file['fileName']
        );
        if (!$fileMoved) {
            return null;
        }

        if ($operationFile->validate()) {
            $operationFile->save(false);
        } else {
            return null;
        }

        return $operationFile;
    }

    /**
     * Объект связанного поля.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperation()
    {
        return $this->hasOne(Operation::className(), ['uuid' => 'operationUuid']);
    }

}
