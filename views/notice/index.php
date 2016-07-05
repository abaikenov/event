<?php

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'My notices');
?>
<div class="notice-index">

    <h1><?= Yii::t('app', 'My notices')?></h1>
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_list',
        'itemOptions' => ['class' => 'notice-item']
    ]) ?>
</div>
