<?php

namespace app\controllers;

use app\components\AccessBehavior;
use app\models\News;
use app\models\Notice;
use app\models\Notification;
use app\models\RegistrationForm;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class NoticeController extends Controller
{
    public function behaviors()
    {
        return [
            'as AccessBehavior' => [
                'class' => AccessBehavior::className(),
                'rules' =>
                    ['notice' =>
                        [
                            [
                                'actions' => ['index', 'view'],
                                'allow' => true,
                                'roles' => ['@']
                            ],
                        ]
                    ]
            ],
        ];
    }

    public function actionIndex()
    {
        $pagination = new Pagination();
        if(!Yii::$app->user->isGuest) {
            $pagination->pageSize = Yii::$app->user->identity->getPreference('page_size');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Notice::findMyNotice(),
            'pagination' => $pagination
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
