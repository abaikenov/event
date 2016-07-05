<?php

/* @var $this yii\web\View */

$this->title = 'News';
?>
<div class="site-index">

    <h1>Новости</h1>
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_list',
        'itemOptions' => ['class' => 'news-item']
    ]) ?>
</div>
