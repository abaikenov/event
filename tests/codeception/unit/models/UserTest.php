<?php

namespace tests\codeception\unit\models;

use app\models\RegistrationForm;
use app\models\User;
use Codeception\Specify;
use yii\codeception\TestCase;

class UserTest extends TestCase
{
    use Specify;

    protected function tearDown()
    {
        $model = User::find()->where(['username' => 'Username'])->one();
        if(null != $model)
            $model->delete();
        parent::tearDown();
    }

    public function testCreateUser()
    {
        $model = new User();
        $model->setScenario('create');
        $model->generateAuthKey();
        $model->attributes = [
            'username' => 'Username',
            'email' => 'test@gmail.com',
        ];

        $this->specify('user should contain correct data', function () use ($model) {
            expect('must be validated', $model->validate())->true();
            expect('must be saved', $model->save())->true();
            expect('status must be inactive', $model->status)->equals(0);
            expect('auth_key must be not empty', $model->auth_key)->notEmpty();
            expect('password_hash must be empty', $model->password_hash)->isEmpty();
        });
    }

    public function testCreateNoUser()
    {
        $model = new User();
        $model->setScenario('create');
        $model->generateAuthKey();
        $model->attributes = [
            'username' => '',
            'email' => '',
        ];


        $this->specify('user should contain correct data', function () use ($model) {
            expect('must not be validated', $model->validate())->false();
            expect('username must be error', $model->errors)->hasKey('username');
            expect('email must be error', $model->errors)->hasKey('email');
        });
    }

    public function testCreateUserWithWrongEmail()
    {
        $model = new User();
        $model->setScenario('create');
        $model->generateAuthKey();
        $model->attributes = [
            'username' => 'Username',
            'email' => 'wrong_email',
        ];

        $this->specify('user should contain correct data', function () use ($model) {
            expect('must not be validated', $model->validate())->false();
            expect('email must be error', $model->errors)->hasKey('email');
        });
    }

    public function testSignup()
    {
        $model = new RegistrationForm();
        $model->attributes = [
            'username' => 'Username',
            'email' => 'test@gmail.com',
            'password' => '12345678',
            'confirmPassword' => '12345678',
        ];

        $this->specify('user should contain correct data', function () use ($model) {
            expect('signup() must not be null', $model->signup())->notNull();
        });
    }
}
