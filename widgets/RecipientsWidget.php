<?php
/**
 * Created by PhpStorm.
 * User: a.baikenov
 * Date: 30.06.2016
 * Time: 17:45
 */

namespace app\widgets;


use app\models\User;
use Yii;
use yii\bootstrap\Widget;

class RecipientsWidget extends Widget
{
    public $model;
    public $attribute;
    public $items;

    public function init()
    {
        parent::init();

        $users = User::listAll(); $roles = []; $selection = [];
        foreach (Yii::$app->authManager->getRoles() as $role) {
            $roles[$role->name] = $role->description;
        }

        foreach ($this->model->getRecipients()->all() as $item) {
            $selection[$item->group][] = $item->group_id;
        }

        $this->items = [
            [
                'title' => Yii::t('app', 'Roles'),
                'name' => 'role',
                'items' => $roles,
                'selection' => isset($selection['role']) ? $selection['role'] : null
            ],
            [
                'title' => Yii::t('app', 'Users'),
                'name' => 'user',
                'items' => $users,
                'selection' => isset($selection['user']) ? $selection['user'] : null
            ],
            [
                'title' => Yii::t('app', 'Others'),
                'name' => 'other',
                'items' => ['yourself' => Yii::t('app', 'yourself')],
                'selection' => isset($selection['other']) ? $selection['other'] : null
            ],
        ];
    }

    public function run()
    {
        
        
        return $this->render('recipients', [
            'model' => $this->model,
            'attribute' => $this->attribute,
            'items' => $this->items,
        ]);
    }
}