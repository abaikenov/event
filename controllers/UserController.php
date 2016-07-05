<?php

namespace app\controllers;

use app\components\AccessBehavior;
use app\models\CreatePasswordForm;
use app\models\Preferences;
use app\models\SettingForm;
use app\models\User;
use app\models\UserPreference;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'as AccessBehavior' => [
                'class' => AccessBehavior::className(),
                'rules' =>
                    ['user' =>
                        [
                            [
                                'actions' => ['preference'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],
                            [
                                'actions' => ['confirm'],
                                'allow' => true,
                                'roles' => ['?'],
                            ],
                        ],
                    ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->setScenario('create');
        $model->generateAuthKey();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionConfirm($authKey)
    {
        $user = User::findByAuthKey($authKey);
        if (null != $user) {
            if(!$user->password_hash) {
                $model = new CreatePasswordForm();
                $model->user = $user;
                
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $user = $model->createPassword();
                } else {
                    return $this->render('create_password', [
                        'model' => $model,
                    ]);
                }
            }
            $user->doActivate();
            Yii::$app->user->login($user);
            $this->goHome();
        } else {
            Yii::$app->session->setFlash('confirm', Yii::t('app', 'Cannot find user by your auth key'));
            $this->redirect(Url::toRoute('site/login'));
        }
    }

    public function actionPreference()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            foreach (Yii::$app->request->post() as $key => $value) {
                if($model = Preferences::findOne(['option' => $key])) {
                    $model->unlink('user', Yii::$app->user->identity, true);
                    $model->link('user', Yii::$app->user->identity, ['value' => $value == 'on' ? 1 : $value]);
                }
            }
            return ['msg' => 'saved'];
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
