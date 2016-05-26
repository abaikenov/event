<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="small">Дата отправки уведомления: <?= $model->date?></p>
    <p class="small">От: <?= $model->getSender()->one()->username?></p>
    <p><?= $model->text ?></p>

</div>
