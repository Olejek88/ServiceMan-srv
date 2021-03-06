<?php

namespace backend\models;

use common\models\Objects;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Exception;

/**
 * ObjectSearch represents the model behind the search form about `common\models\Object`.
 */
class ObjectsSearch extends Objects
{
    public $fullTitle;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'title', 'fullTitle', 'house', 'houseUuid', 'objectTypeUuid', 'createdAt', 'changedAt'], 'safe'],
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
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function search($params)
    {
        $query = Objects::find();
        $query->joinWith('house');
        $query->joinWith('house.street');
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
            'houseUuid' => $this->houseUuid,
            'objectStatusUuid' => $this->objectStatusUuid,
            'objectTypeUuid' => $this->objectTypeUuid,
            'object.deleted' => false,
            'house.deleted' => false,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'object.title', $this->title]);

        $query->andFilterWhere([
            'or',
            ['like', 'house.number', '%' . $this->fullTitle . '%', false],
            ['like', 'street.title', '%' . $this->fullTitle . '%', false]
        ]);

        return $dataProvider;
    }
}
