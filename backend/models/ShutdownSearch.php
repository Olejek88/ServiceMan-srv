<?php

namespace backend\models;

use common\models\Objects;
use common\models\Shutdown;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ShutdownSearch represents the model behind the search form about `common\models\Shutdown`.
 */
class ShutdownSearch extends Shutdown
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'contragentUuid', 'startDate', 'endDate', 'changedAt'], 'safe'],
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
        $query = Objects::find();

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
            'contragentUuid' => $this->contragentUuid,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        return $dataProvider;
    }
}
