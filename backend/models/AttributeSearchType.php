<?php

namespace backend\models;

use common\models\AttributeType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AttributeSearchType represents the model behind the search form about `common\models\AttributeType`.
 */
class AttributeSearchType extends AttributeType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id','refresh','type'], 'integer'],
            [['name','units'], 'string'],
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
        $query = AttributeType::find();

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
            'type' => $this->type,
            'units' => $this->units,
            'refresh' => $this->refresh,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
