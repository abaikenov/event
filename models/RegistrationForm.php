<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\helpers\Url;

/**
 * RegistrationForm is the model behind the login form.
 */
class RegistrationForm extends Model
{
    public $username;
    public $email;
    public $phone;
    public $password;
    public $confirmPassword;

    private $user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'unique', 'targetClass' => 'app\models\User', 'message' => Yii::t('app', 'Username is not unique')],

            ['email', 'required'],
            ['email', 'email', 'checkDNS' => true],
            ['email', 'unique', 'targetClass' => 'app\models\User', 'message' => Yii::t('app', 'Email is not unique')],

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
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'confirmPassword' => Yii::t('app', 'Confirm Password'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate() === true) {
            $user = new User();
            $user->setScenario('signup');
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();

            if ($user->save() === false) {
                return null;
            }

            return $user;
        }

        return null;

    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    private function getUser()
    {
        if ($this->user === false) {
            $this->user = User::findByUsername($this->username);
        }
        return $this->user;
    }

    public function checked($attribute)
    {
        if (!$this->$attribute)
            $this->addError($attribute, Yii::t('app', 'Agreement error'));
    }
}
