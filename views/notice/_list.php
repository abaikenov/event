<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
?>

<div class="col-sm-12">
    <p class="lead"><?= $model->title ?></p>
    <div class="info">
        <span class="info"><strong><?= Yii::t('app', 'From')?></strong> <?= $model->sender->username?></span><br/>
        <span class="info"><i class="glyphicon glyphicon-time"></i> <?= $model->getDate()?></span>
    </div>
    <br/>
    <?= $model->text ?>
</div>
<div class="clearfix"></div>
<hr>