<?php

namespace backend\models;

use common\models\EquipmentRegister;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipmentRegisterSearch represents the model behind the search form about `common\models\EquipmentRegister`.
 */
class EquipmentRegisterSearch extends EquipmentRegister
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userUuid', 'registerType', 'equipmentUuid', 'date'], 'safe'],
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
        $query = EquipmentRegister::find();

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
            'date' => $this->date,
            'registerType' => $this->registerType,
            'equipmentUuid' => $this->equipmentUuid,
            'userUuid' => $this->userUuid,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'equipmentUuid', $this->equipmentUuid])
            ->andFilterWhere(['like', 'registerType', $this->registerType])
            ->andFilterWhere(['like', 'userUuid', $this->userUuid])
            ->orderBy(['date' => SORT_DESC]);

        return $dataProvider;
    }
}
