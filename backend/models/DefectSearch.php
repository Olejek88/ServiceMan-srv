<?php

namespace backend\models;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Defect;
use yii\db\Exception;

/**
 * DefectSearch represents the model behind the search form about `common\models\Defect`.
 */
class DefectSearch extends Defect
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'userUuid', 'date', 'equipmentUuid', 'defectTypeUuid', 'defectStatus', 'title', 'taskUuid', 'createdAt', 'changedAt'], 'safe'],
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
        $query = Defect::find();
        $query->joinWith('equipment.object');
        $query->joinWith('equipment.object.house');
        $query->joinWith('equipment.object.house.street');

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
            'defectTypeUuid' => $this->defectTypeUuid,
            'userUuid' => $this->userUuid,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'equipmentUuid', $this->equipmentUuid])
            ->andFilterWhere(['like', 'defectStatus', $this->defectStatus]);

        $query->orderBy('changedAt DESC');
        return $dataProvider;
    }
}
