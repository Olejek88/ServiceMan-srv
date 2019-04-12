<?php

namespace backend\models;

use common\models\UserSystem;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSystemSearch represents the model behind the search form about `common\models\UserSystem`.
 */
class UserSystemSearch extends UserSystem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'houseUuid', 'userUuid', 'createdAt', 'changedAt'], 'safe'],
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
        $query = UserSystem::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            '_id' => $this->_id,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'equipmentSystemUuid', $this->equipmentSystemUuid])
            ->andFilterWhere(['like', 'userUuid', $this->userUuid])
            ->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }
}
