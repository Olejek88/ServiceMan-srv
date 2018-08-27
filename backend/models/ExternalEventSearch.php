<?php

namespace backend\models;

use common\models\ExternalEvent;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ExternalEventSearch represents the model behind the search form about `common\models\ExternalEvent`.
 */
class ExternalEventSearch extends ExternalEvent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'status', 'verdict'], 'integer'],
            [['uuid', 'tagUuid', 'date', 'actionUuid'], 'string'],
            [['uuid', 'createdAt', 'changedAt'], 'safe'],
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
        $query = ExternalEvent::find();

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
            'date' => $this->date,
            'status' => $this->status,
            'verdict' => $this->verdict
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'tagUuid', $this->tagUuid])
            ->andFilterWhere(['like', 'actionUuid', $this->actionUuid])
            ->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }
}
