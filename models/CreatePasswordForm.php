<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\helpers\Url;

/**
 * CreatePasswordForm.
 */
class CreatePasswordForm extends Model
{
    public $user;
    public $password;
    public $confirmPassword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'confirmPassword'], 'required'],
            ['password', 'string', 'min' => 8],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Yii::t('app', 'Password'),
            'confirmPassword' => Yii::t('app', 'Confirm Password'),
        ];
    }

    public function createPassword()
    {
        if ($this->validate() === true) {
            $this->user->setScenario('create_password');
            $this->user->setPassword($this->password);

            if ($this->user->save() === false) {
                return null;
            }

            return $this->user;
        }

        return null;

    }
}
