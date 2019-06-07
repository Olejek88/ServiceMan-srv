<?php


namespace common\components;


use yii\db\ActiveQuery;

class ZhkhActiveQuery extends ActiveQuery
{

    public function where($condition, $params = [])
    {
        if (empty($this->where)) {
            $this->where = $condition;
            $this->addParams($params);
        } else {
            $this->andWhere($condition, $params);
        }

        return $this;
    }

}