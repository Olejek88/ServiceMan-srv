<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Views
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TaskEquipmentStage as TaskEquipmentStageModel;

/**
 * TaskEquipmentStage represents the model behind the search
 * form about `common\models\TaskEquipmentStage`.
 *
 * @category Category
 * @package  Backend\models
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class TaskEquipmentStageSearch extends TaskEquipmentStageModel
{
    /**
     * Rules.
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [
                [
                    'uuid',
                    'taskTemplateUuid',
                    'equipmentStageUuid',
                    'period',
                    'createdAt',
                    'changedAt'
                ],
                'safe'
            ],
        ];
    }

    /**
     * Scenarios.
     *
     * @inheritdoc
     *
     * @return array
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params Params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TaskEquipmentStageModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider(['query' => $query,]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to
            // return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        //$query->andFilterWhere(
        //    [
        //        '_id' => $this->_id,
        //        'createdAt' => $this->createdAt,
        //        'changedAt' => $this->changedAt,
        //    ]
        //);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'taskTemplateUuid', $this->taskTemplateUuid])
            ->andFilterWhere(
                ['like', 'equipmentStageUuid', $this->equipmentStageUuid]
            )
            ->andFilterWhere(['like', 'period', $this->period]);

        return $dataProvider;
    }
}
