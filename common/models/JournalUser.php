<?php

namespace common\models;

use yii\db\ActiveRecord;
/**
 * This is the model class for table "journal_user".
 *
 * @property integer $_id
 * @property string $userId
 * @property string $address
 * @property string $data
 *
 * @property User $userId0
 */
class JournalUser extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'journal_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'address'], 'required'],
            [['data'], 'safe'],
            [['userId'], 'integer', 'max' => 10],
            [['address'], 'string', 'max' => 15],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'Id',
            'userId' => 'User Email',
            'address' => 'Address',
            'data' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEmail0()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
}
