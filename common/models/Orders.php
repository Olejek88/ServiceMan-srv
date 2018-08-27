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

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "Orders".
 *
 * @category Category
 * @package  Common\models
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $authorUuid
 * @property string $userUuid
 * @property string $receivDate
 * @property string $startDate
 * @property string $openDate
 * @property string $closeDate
 * @property string $orderStatusUuid
 * @property string $orderVerdictUuid
 * @property string $attemptSendDate
 * @property integer $attemptCount
 * @property integer $updated
 * @property string $createdAt
 * @property string $changedAt
 * @property string orderLevelUuid
 * @property string customerUuid
 * @property string perpetratorUuid
 * @property string $comment
 * @property string $reason
 */
class Orders extends ActiveRecord
{

    /**
     * Behaviors.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
                // 'value' => new Expression('CONCAT(CURDATE(),"T",CURTIME())'),
            ],
        ];
    }

    /**
     * Название таблицы.
     *
     * @return string
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
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
            [
                [
                    'uuid',
                    'title',
                    'authorUuid',
                    'userUuid',
                    'orderStatusUuid',
                    'orderVerdictUuid',
                    'attemptCount',
                    'updated'
                ],
                'required'
            ],
            [
                [
                    'receivDate',
                    'startDate',
                    'openDate',
                    'closeDate',
                    'attemptSendDate',
                    'createdAt',
                    'changedAt'
                ],
                'safe'
            ],
            [['attemptCount', 'updated'], 'integer'],
            [
                [
                    'uuid',
                    'authorUuid',
                    'userUuid',
                    'orderStatusUuid',
                    'orderVerdictUuid',
                    'orderLevelUuid'
                ],
                'string',
                'max' => 45
            ],
            [['title','comment','reason'], 'string', 'max' => 100],
        ];
    }

    /**
     * Fields.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'title',
            'author' => function ($model) {
                return $model->author;
            },
            'user' => function ($model) {
                return $model->user;
            },
            'orderStatus' => function ($model) {
                return $model->orderStatus;
            },
            'orderVerdict' => function ($model) {
                return $model->orderVerdict;
            }, 'receivDate', 'startDate', 'openDate', 'closeDate',
            'attemptSendDate', 'attemptCount', 'updated', 'createdAt', 'changedAt',
            'comment','reason',
            'tasks' => function ($model) {
                return $model->tasks;
            },
            'orderLevel' => function ($model) {
                return $model->orderLevel;
            },
        ];
    }

    /**
     * Scenarios description.
     *
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = [
            'uuid',
            'title',
            'authorUuid',
            'userUuid',
            'orderStatusUuid',
            'orderVerdictUuid',
            'attemptCount',
            'updated',
            'createdAt',
            'changedAt',
            'orderLevelUuid'
        ];

        return $scenarios;
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
            'title' => Yii::t('app', 'Название'),
            'comment' => Yii::t('app', 'Комментарий'),
            'reason' => Yii::t('app', 'Основание'),
            'authorUuid' => Yii::t('app', 'Автор'),
            'userUuid' => Yii::t('app', 'Исполнитель'),
            'receivDate' => Yii::t('app', 'Дата получения'),
            'startDate' => Yii::t('app', 'Назначен'),
            'openDate' => Yii::t('app', 'Дата открытия'),
            'closeDate' => Yii::t('app', 'Дата закрытия'),
            'orderStatusUuid' => Yii::t('app', 'Статус'),
            'orderVerdictUuid' => Yii::t('app', 'Вердикт'),
            'attemptSendDate' => Yii::t('app', 'Дата отправки'),
            'attemptCount' => Yii::t('app', 'Количество отправок'),
            'updated' => Yii::t('app', 'Обновления'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Возвращает автора наряда.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Users::className(), ['uuid' => 'authorUuid']);
    }

    /**
     * Возвращает пользователя.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['uuid' => 'userUuid']);
    }

    /**
     * Возвращает статус наряда.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderStatus()
    {
        return $this->hasOne(
            OrderStatus::className(), ['uuid' => 'orderStatusUuid']
        );
    }

    /**
     * Возвращает вердикт.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderVerdict()
    {
        return $this->hasOne(
            OrderVerdict::className(), ['uuid' => 'orderVerdictUuid']
        );
    }

    /**
     * Возвращает задачи наряда.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['orderUuid' => 'uuid']);
    }

    /**
     * Возвращает уровень? наряда.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderLevel()
    {
        return $this->hasOne(OrderLevel::className(), ['uuid' => 'orderLevelUuid']);
    }

    /**
     * Установка статуса наряда.
     *
     * @param string $uuid UUID статуса.
     *
     * @return void
     */
    public function setOrderStatusUuid($uuid)
    {
        $this->setAttribute('orderStatusUuid', $uuid);
    }

    /**
     * Установка статуса уровня наряда?.
     *
     * @param string $uuid UUID статуса.
     *
     * @return void
     */
    public function setOrderLevelUuid($uuid)
    {
        $this->setAttribute('orderLevelUuid', $uuid);
    }

    /**
     * Установка даты последней попытки отправки наряда.
     *
     * @param string $date Дата.
     *
     * @return void
     */
    public function setAttemptSendDate($date)
    {
        $this->setAttribute('attemptSendDate', $date);
    }

    /**
     * Установка количества попыток отправки наряда.
     *
     * @param string $count Попытки.
     *
     * @return void
     */
    public function setAttemptCount($count)
    {
        $this->setAttribute('attemptCount', $count);
    }

    /**
     * Флаг изменения записи для индикации необходимости отправки на сервер.
     *
     * @param integer $updated Флаг.
     *
     * @return void
     */
    public function setUpdated($updated)
    {
        $this->setAttribute('updated', $updated);
    }

    /**
     * Дата открытия/начала наряда?.
     *
     * @param string $date Дата.
     *
     * @return void
     */
    public function setOpenDate($date)
    {
        $this->setAttribute('openDate', $date);
    }

    /**
     * Дата закрытия наряда.
     *
     * @param string $date Дата.
     *
     * @return void
     */
    public function setCloseDate($date)
    {
        $this->setAttribute('closeDate', $date);
    }

    /**
     * Установка вердикта наряда.
     *
     * @param string $uuid UUID вердикта.
     *
     * @return void
     */
    public function setOrderVerdictUuid($uuid)
    {
        $this->setAttribute('orderVerdictUuid', $uuid);
    }
}
