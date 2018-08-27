<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Backend\models
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OperationRepairPart;

/**
 * OperationRepairPartSearch represents the model behind the
 * search form about OperationRepairPart.
 *
 * @category Category
 * @package  Backend\models
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */
class OperationRepairPartSearch extends OperationRepairPart
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
            [['_id', 'quantity'], 'integer'],
            [
                [
                    'uuid',
                    'operationTemplateUuid',
                    'repairPartUuid',
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
        $query = OperationRepairPart::find();

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
        //        'quantity' => $this->quantity,
        //        'createdAt' => $this->createdAt,
        //        'changedAt' => $this->changedAt,
        //    ]
        //);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(
                ['like', 'operationTemplateUuid', $this->operationTemplateUuid]
            )
            ->andFilterWhere(['like', 'repairPartUuid', $this->repairPartUuid]);

        return $dataProvider;
    }
}
