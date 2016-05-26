<?php

/* @var $this yii\web\View */

$this->title = 'My Notifications';
?>
<div class="site-index">

    <h1>Мои уведомления</h1>
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_list'
    ]) ?>
</div>
