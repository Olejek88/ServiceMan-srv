<?php

namespace backend\models;

use common\models\Request;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * RequestSearch represents the model behind the search form about `common\models\Request`.
 */
class RequestSearch extends Request
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'requestStatusUuid', 'requestTypeUuid', 'contragentUuid', 'authorUuid', 'comment', 'userUuid', 'taskUuid',
                'equipmentUuid', 'objectUuid', 'stageUuid', 'closeDate', 'createdAt', 'changedAt'], 'safe'],
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
        $query = Request::find();

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
            'requestStatusUuid' => $this->requestStatusUuid,
            'requestTypeUuid' => $this->requestTypeUuid,
            'comment' => $this->comment,
            'userUuid' => $this->userUuid,
            'closeDate' => $this->closeDate,
            'equipmentUuid' => $this->equipmentUuid,
            'contragentUuid' => $this->contragentUuid,
            'authorUuid' => $this->authorUuid,
            'objectUuid' => $this->objectUuid,
            'taskUuid' => $this->taskUuid,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'requestStatusUuid', $this->requestStatusUuid])
            ->andFilterWhere(['like', 'userUuid', $this->userUuid])
            ->andFilterWhere(['like', 'equipmentUuid', $this->equipmentUuid])
            ->andFilterWhere(['like', 'objectUuid', $this->objectUuid]);

        $query->andFilterWhere(['=', 'closeDate', $this->closeDate]);

        return $dataProvider;
    }
}
