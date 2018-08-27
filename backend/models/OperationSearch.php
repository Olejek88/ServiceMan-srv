<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Operation;

/**
 * OperationSearch represents the model behind the search form about `common\models\Operation`.
 */
class OperationSearch extends Operation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'flowOrder'], 'integer'],
            [['uuid', 'stageUuid', 'operationVerdictUuid', 'operationStatusUuid', 'operationTemplateUuid', 'startDate', 'endDate', 'createdAt', 'changedAt'], 'safe'],
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
        $query = Operation::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // $dataProvider->pagination->pageSize = 25;

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '_id' => $this->_id,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'flowOrder' => $this->flowOrder,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'stageUuid', $this->stageUuid])
            ->andFilterWhere(['like', 'operationVerdictUuid', $this->operationVerdictUuid])
            ->andFilterWhere(['like', 'operationStatusUuid', $this->operationStatusUuid])
            ->andFilterWhere(['like', 'operationTemplateUuid', $this->operationTemplateUuid])
            ->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }
}
