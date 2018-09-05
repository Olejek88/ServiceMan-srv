<?php

namespace backend\models;

use common\models\UserChannel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserChannelSearch represents the model behind the search form about `backend\models\UserChannel`.
 */
class UserChannelSearch extends UserChannel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'active'], 'integer'],
            [['_id', 'createdAt', 'updatedAt'], 'safe'],
            [['messageChannelUuid', 'messageTypeUuid', 'userUuid', 'channelId'], 'safe'],
            [['messageChannelUuid', 'messageTypeUuid', 'userUuid', 'channelId'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserChannel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
