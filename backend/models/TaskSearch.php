<?php

namespace backend\models;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Task;
use yii\db\Exception;

/**
 * TaskSearch represents the model behind the search form about `common\models\Task`.
 */
class TaskSearch extends Task
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'comment', 'workStatusUuid', 'authorUuid', 'taskTemplateUuid', 'taskDate', 'deadlineDate',
                'taskVerdictUuid','startDate', 'endDate', 'createdAt', 'changedAt'], 'safe'],
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
        $query = Task::find();
        $query->joinWith('equipment.object.house');
        $query->joinWith('equipment.object.house.street');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'deadlineDate' => [
                    'asc' => ['deadlineDate' => SORT_ASC],
                    'desc' => ['deadlineDate' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'taskDate' => [
                    'asc' => ['taskDate' => SORT_ASC],
                    'desc' => ['taskDate' => SORT_DESC],
                    'default' => SORT_DESC
                ]
            ],
        ]);
        $dataProvider->sort->defaultOrder = ['taskDate' => SORT_DESC];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'task._id' => $this->_id,
            'taskTemplateUuid' => $this->taskTemplateUuid,
            'taskVerdictUuid' => $this->taskVerdictUuid,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'authorUuid', $this->authorUuid])
            ->andFilterWhere(['like', 'workStatusUuid', $this->workStatusUuid]);

        return $dataProvider;
    }
}
