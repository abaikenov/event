<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-item">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="col-sm-4 pull-left">
        <div class="img">
            <!--  TODO:  доделать вывод изображения из новости   -->
            <img src="/images/empty.jpg"/>
        </div>
        <span class="info"><i class="glyphicon glyphicon-time"></i><?= $model->getDate()?></span>
        <span class="info"><i class="glyphicon glyphicon-eye-open"></i><?= $model->visit?></span>
    </div>
    <p><?= $model->text ?></p>
</div>
