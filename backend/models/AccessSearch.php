<?php

namespace backend\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;

/**
 * ActionSearchType represents the model behind the search form about `AccessModel`.
 */
class AccessSearch extends AccessModel
{

    private $filterModel = false;
    private $filterPermission = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'permission'], 'string'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ArrayDataProvider
     * @throws InvalidConfigException
     */
    public function search($params)
    {
        if ($this->load($params) || $this->load(Yii::$app->request->getBodyParams())) {
            $this->filterModel = $this->model != '';
            $this->filterPermission = $this->permission != '';
        }

        $am = Yii::$app->getAuthManager();
        $roles = $am->getRoles();
        $permissions = $am->getPermissions();

        $grouped = [];
        foreach ($permissions as $permission) {
            if ($permission->type == 2) {
                if (preg_match('/([a-z]*)([A-Z].*)/', $permission->name, $match)) {
                    if ($this->filterModel && $this->filterPermission) {
                        if (preg_match('/' . $this->model . '/i', $match[2]) && preg_match('/' . $this->permission . '/i', $match[1])) {
                            $grouped[$match[2]][$match[1]] = $permission->name;
                        }
                    } elseif ($this->filterModel && !$this->filterPermission) {
                        if (preg_match('/' . $this->model . '/i', $match[2])) {
                            $grouped[$match[2]][$match[1]] = $permission->name;
                        }
                    } elseif (!$this->filterModel && $this->filterPermission) {
                        if (preg_match('/' . $this->permission . '/i', $match[1])) {
                            $grouped[$match[2]][$match[1]] = $permission->name;
                        }
                    } else {
                        $grouped[$match[2]][$match[1]] = $permission->name;
                    }
                }
            }
        }

        $permsByRole = [];
        foreach ($roles as $role) {
            $permsByRole[$role->name] = $am->getPermissionsByRole($role->name);
        }

        $data = [];
        $idx = 0;
        ksort($grouped);
        foreach ($grouped as $model => $permission) {
            foreach ($permission as $shortName => $value) {
                $accessModel = new AccessModel();
                $accessModel->id = $idx++;
                $className = 'common\\models\\' . $model;
                $accessModel->model = $className::DESCRIPTION . ' (' . $model . ')';
                $accessModel->permission = $shortName;
                foreach ($permsByRole as $name => $role) {
                    if (isset($role[$value])) {
                        $roleName = $name;
                        $accessModel->$roleName = true;
                    }
                }

                $data[] = $accessModel;
            }
        }

        unset($roles);
        unset($permissions);
        unset($permsByRole);

        $dataProvider = new ArrayDataProvider();
        $dataProvider->allModels = $data;
        $dataProvider->pagination = [
            'pageSize' => 20,
        ];

        return $dataProvider;
    }
}
