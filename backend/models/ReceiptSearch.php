<?php

namespace backend\models;

use common\models\Receipt;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ReceiptSearch represents the model behind the search form about `common\models\Receipt`.
 */
class ReceiptSearch extends Receipt
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'contragentUuid', 'date', 'userUuid', 'requestUuid', 'description', 'result', 'closed', 'latitude', 'longitude', 'createdAt', 'changedAt'], 'safe'],
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
        $query = Receipt::find();

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
            'userUuid' => $this->userUuid,
            'contragentUuid' => $this->contragentUuid,
            'requestUuid' => $this->requestUuid,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }
}
