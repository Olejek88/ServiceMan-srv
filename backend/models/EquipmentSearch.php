<?php

namespace backend\models;

use common\models\Equipment;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Exception;

/**
 * EquipmentSearch represents the model behind the search form about `common\models\Equipment`.
 */

class EquipmentSearch extends Equipment
{
    public $nextDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'equipmentTypeUuid', 'objectUuid', 'testDate', 'nextDate', 'inputDate', 'equipmentStatusUuid',
                'serial', 'tag', 'createdAt', 'changedAt'], 'safe'],
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
        $query = Equipment::find();
        $query->joinWith('object.house');
        $query->joinWith('object.house.street');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'testDate',
                'inputDate'
            ]
        ]);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '_id' => $this->_id,
            'equipment.deleted' => false,
            'object.deleted' => false,
            'house.deleted' => false,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'objectUuid', $this->objectUuid])
            ->andFilterWhere(['like', 'equipmentTypeUuid', $this->equipmentTypeUuid])
            ->andFilterWhere(['like', 'equipmentStatusUuid', $this->equipmentStatusUuid])
            ->andFilterWhere(['like', 'serial', $this->serial])
            ->orderBy(['changedAt' => SORT_DESC]);

        if(isset ($this->nextDate)&&$this->nextDate!=''){
            $date_explode=explode(" - ",$this->nextDate);
            $date1=trim($date_explode[0]);
            $date2=trim($date_explode[1]);
            $query->andFilterWhere(['between','nextDate',$date1,$date2]);
        }
        if(isset ($this->inputDate)&&$this->inputDate!=''){
            $date_explode=explode(" - ",$this->inputDate);
            $date1=trim($date_explode[0]);
            $date2=trim($date_explode[1]);
            $query->andFilterWhere(['between','inputDate',$date1,$date2]);
        }
        return $dataProvider;
    }
}
