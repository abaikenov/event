<?php
use yii\bootstrap\Html;
use yii\helpers\Url;

?>

<div class="alert alert-<?= $model->view ? 'info' : 'danger'?>">
    <h4><?= $model->title?></h4>
    <p class="small">Дата отправки уведомления: <?= $model->date?></p>
    <p class="small">От: <?= $model->getSender()->one()->username?></p>
    <?= Html::a("посмотреть", Url::to(['view', 'id' => $model->id]))?>
</div>