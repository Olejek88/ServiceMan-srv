<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OperationTemplate;

/**
 * OperationSearchTemplate represents the model behind the
 * search form about `common\models\OperationTemplate`.
 */
class OperationSearchTemplate extends OperationTemplate
{
    /**
     * Rules
     *
     * @return array
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [
                [
                    'uuid',
                    'title',
                    'description',
                    'createdAt',
                    'changedAt'
                ],
                'safe'
            ],
        ];
    }

    /**
     * Scenarios
     *
     * @return array
     *
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
     * @param array $params Params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = OperationTemplate::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider(['query' => $query,]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to
            // return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
