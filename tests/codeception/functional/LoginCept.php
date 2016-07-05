<?php

use tests\codeception\_pages\LoginPage;
use yii\helpers\Url;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that login works');

$loginPage = LoginPage::openBy($I);

$I->see(Yii::t('app', 'Login'), 'h1');

$I->amGoingTo('try to login with empty credentials');
$loginPage->login('', '');
$I->expectTo('see validations errors');
$I->see(Yii::t('app', 'Username cannot be blank.'));
$I->see('Password cannot be blank.');

$I->amGoingTo('try to login with wrong credentials');
$loginPage->login('admin', 'wrong');
$I->expectTo('see validations errors');
$I->see('Incorrect username or password.');

$I->amGoingTo('try to login with correct credentials');
$loginPage->login('admin', 'admin');
$I->expectTo('see user info');
$I->seeLink(Yii::t('app', 'Home'), Url::toRoute(['/site/index']));
$I->seeLink(Yii::t('app', 'News'), Url::toRoute(['/news']));
$I->seeLink(Yii::t('app', 'Users'), Url::toRoute(['/user']));
$I->seeLink(Yii::t('app', 'Notification'), Url::toRoute(['/notification']));
$I->seeLink(Yii::t('app', 'Notification Types'), Url::toRoute(['/notification-type']));
