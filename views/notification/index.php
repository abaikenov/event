<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Notification');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if(Yii::$app->session->getFlash('send')):?>
        <p class="alert alert-info"><?= Yii::$app->session->getFlash('send')?></p>
    <?php endif;?>
    <p>
        <?= Html::a(Yii::t('app', 'Create Notification'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'event',
            'model',
            'attribute',
            [
                'attribute' => 'from',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->getSenderDisplayName();
                }
            ],
            [
                'attribute' => 'recipients',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->getRecipientsForDisplay();
                }
            ],
            'type_ids_name',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{send}&nbsp;&nbsp;{delete}',
                'buttons' =>
                    [
                        'send' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-send"></span>', Url::to(['send', 'id' => $model->id]), [
                                'title' => Yii::t('yii', 'Send notification')
                            ]);
                        },
                    ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
