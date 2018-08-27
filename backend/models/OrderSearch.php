<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Orders;
use kartik\daterange\DateRangeBehavior;

/**
 * OrdersSearch represents the model behind the search form about `common\models\Orders`.
 */
class OrderSearch extends Orders
{
    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'createTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'attemptCount', 'updated'], 'integer'],
            [['uuid', 'title', 'authorUuid', 'userUuid', 'receivDate', 'startDate', 'openDate', 'closeDate', 'orderStatusUuid', 'orderVerdictUuid', 'attemptSendDate', 'createdAt', 'changedAt'], 'safe'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
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
        $query = Orders::find();

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
            'receivDate' => $this->receivDate,
            'startDate' => $this->startDate,
            'openDate' => $this->openDate,
            'closeDate' => $this->closeDate,
            'attemptSendDate' => $this->attemptSendDate,
            'attemptCount' => $this->attemptCount,
            'updated' => $this->updated,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'authorUuid', $this->authorUuid])
            ->andFilterWhere(['like', 'userUuid', $this->userUuid])
            ->andFilterWhere(['like', 'orderStatusUuid', $this->orderStatusUuid])
            ->andFilterWhere(['like', 'orderVerdictUuid', $this->orderVerdictUuid]);

        $query->andFilterWhere(['>=', 'startDate', $this->createTimeStart])
            ->andFilterWhere(['<', 'startDate', $this->createTimeEnd]);

        return $dataProvider;
    }
}
