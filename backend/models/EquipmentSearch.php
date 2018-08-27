<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Equipment;

/**
 * EquipmentSearch represents the model behind the search form about `common\models\Equipment`.
 */
class EquipmentSearch extends Equipment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'equipmentModelUuid', 'title', 'criticalTypeUuid', 'startDate', 'tagId', 'image', 'equipmentStatusUuid', 'inventoryNumber', 'serialNumber', 'locationUuid', 'createdAt', 'changedAt'], 'safe'],
            [['latitude', 'longitude'], 'number'],
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
        $query = Equipment::find();

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
            'startDate' => $this->startDate,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'equipmentModelUuid', $this->equipmentModelUuid])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'criticalTypeUuid', $this->criticalTypeUuid])
            ->andFilterWhere(['like', 'tagId', $this->tagId])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'equipmentStatusUuid', $this->equipmentStatusUuid])
            ->andFilterWhere(['like', 'inventoryNumber', $this->inventoryNumber])
            ->andFilterWhere(['like', 'serialNumber', $this->serialNumber])
            ->andFilterWhere(['like', 'locationUuid', $this->locationUuid])
            ->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }
}
