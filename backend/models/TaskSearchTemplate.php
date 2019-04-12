<?php
namespace backend\models;

use common\models\TaskType;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TaskTemplate;

/**
 * TaskSearchTemplate represents the model behind the
 * search form about `common\models\TaskTemplate`.
 */
class TaskSearchTemplate extends TaskTemplate
{
    public $taskType;

    /**
     * Rules
     *
     * @return array
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'normative'], 'integer'],
            [
                [
                    'uuid',
                    'title',
                    'description',
                    'taskTypeUuid',
                    'taskType',
                    'normative',
                    'createdAt',
                    'changedAt'
                ],
                'safe'
            ],
        ];
    }

    /**
     * Связанное поле.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskType()
    {
        return $this->hasOne(TaskType::class, ['uuid' => 'taskTypeUuid']);
    }

    /**
     * Scenarios
     *
     * @return array
     *
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
     * @param array $params Param
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TaskTemplate::find();
        $query->joinWith(['taskType']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $dataProvider->sort->attributes['taskType'] = [
            'asc' => ['task_type.title' => SORT_ASC],
            'desc' => ['task_type.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do notwant to
            // return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        //$query->andFilterWhere(
        //    [
        //        '_id' => $this->_id,
        //        'normative' => $this->normative,
        //        'task_type.title' => $this->taskType,
        //        'createdAt' => $this->createdAt,
        //        'changedAt' => $this->changedAt,
        //    ]
        //);
        $tempTbl = 'task_template';
        $query->andFilterWhere(['like', $tempTbl.'.uuid', $this->uuid])
            ->andFilterWhere(['like', $tempTbl.'.title', $this->title])
            ->andFilterWhere(['like', $tempTbl.'.description', $this->description])
            ->andFilterWhere(['like', $tempTbl.'._id', $this->_id])
            ->andFilterWhere(
                ['like', $tempTbl.'.taskTypeUuid', $this->taskTypeUuid]
            );

        return $dataProvider;
    }
}
