<?php

namespace backend\models;

use common\models\Request;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Exception;

/**
 * RequestSearch represents the model behind the search form about `common\models\Request`.
 */
class RequestSearch extends Request
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'type', 'requestStatusUuid', 'requestTypeUuid', 'authorUuid', 'comment', 'contragentUuid', 'taskUuid',
                'equipmentUuid', 'objectUuid', 'stageUuid', 'closeDate', 'createdAt', 'changedAt'], 'safe'],
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
        $query = Request::find();
        $query->joinWith('object.house');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['_id' => SORT_DESC]]
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
            'requestStatusUuid' => $this->requestStatusUuid,
            'requestTypeUuid' => $this->requestTypeUuid,
            'comment' => $this->comment,
            'contragentUuid' => $this->contragentUuid,
            'closeDate' => $this->closeDate,
            'equipmentUuid' => $this->equipmentUuid,
            'authorUuid' => $this->authorUuid,
            'objectUuid' => $this->objectUuid,
            'taskUuid' => $this->taskUuid,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        $query->andFilterWhere(['=', 'closeDate', $this->closeDate]);

        return $dataProvider;
    }
}
