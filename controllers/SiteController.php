<?php

namespace app\controllers;

use app\components\AccessBehavior;
use app\models\News;
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

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'as AccessBehavior' => [
                'class' => AccessBehavior::className(),
                'rules' =>
                    ['site' =>
                        [
                            [
                                'actions' => ['index'],
                                'allow' => true,
                                'roles' => ['admin', 'authorized', 'moderator', '?'],
                            ],
                            [
                                'actions' => ['login', 'signup'],
                                'allow' => true,
                            ],
                            [
                                'actions' => ['logout'],
                                'allow' => true,
                                'roles' => ['@']
                            ],
                        ]
                    ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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
            'query' => News::find()->orderBy('created_at desc'),
            'pagination' => $pagination
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = News::findOne($id);
        $model->incVisit();
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegistrationForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $model->signup();

            if ($user !== null) {
                Yii::$app->session->setFlash('signup', Yii::t('app', 'Registration completed successfully! Check your email for confirm it.'));
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);

    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
