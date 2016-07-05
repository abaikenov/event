<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
?>

<div class="col-sm-4">
    <div class="img">
        <!--  TODO:  доделать вывод изображения из новости   -->
        <img src="/images/empty.jpg"/>
    </div>
    <span class="info"><i class="glyphicon glyphicon-time"></i><?= $model->getDate()?></span>
    <span class="info"><i class="glyphicon glyphicon-eye-open"></i><?= $model->visit?></span>
</div>
<div class="col-sm-8">
    <p class="lead"><?= $model->title ?></p>
    <?= $model->announce ?>
    <p><a href="<?= Url::to(['view', 'id' => $model->id]) ?>"><?= Yii::t('app', 'read more...')?></a></p>
</div>
<div class="clearfix"></div>
<hr>