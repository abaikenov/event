<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * SettingForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class SettingForm extends Model
{
    public $page_size;
    public $notification;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['page_size', 'required'],
            ['page_size', 'integer', 'min' => 1, 'max' => 100],
            ['page_size', 'default', 'value' => 10],
            ['notification', 'safe']
        ];
    }

    public function init()
    {
        parent::init();
        $this->getUser();
        $setting = $this->_user->getSetting();
        if(isset($setting['page_size']))
            $this->page_size = $setting['page_size'];

        if(isset($setting['notification']))
            $this->notification = $setting['notification'];
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Yii::$app->getUser()->getIdentity();
        }

        return $this->_user;
    }

    public function save()
    {
        return $this->_user->saveSetting($this->getAttributes());
    }
}
