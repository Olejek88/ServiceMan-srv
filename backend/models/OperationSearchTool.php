<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Backend\views
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OperationTool;

/**
 * OperationSearchTool represents the model behind the
 * search form about `common\models\OperationTool`.
 *
 * @category Category
 * @package  Backend\views
 * @author   Максим Шумаков <ms.profile.d@gmail.com>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class OperationSearchTool extends OperationTool
{
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
            [['_id'], 'integer'],
            [
                [
                    'uuid',
                    'operationTemplateUuid',
                    'operationTemplate',
                    'toolUuid',
                    'tool',
                    'createdAt',
                    'changedAt'
                ],
                'safe'
            ],
        ];
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
     * @param array $params Params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = OperationTool::find();

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
            ->andFilterWhere(
                ['like', 'operationTemplateUuid', $this->operationTemplateUuid]
            )
            ->andFilterWhere(['like', 'toolUuid', $this->toolUuid]);

        return $dataProvider;
    }
}
