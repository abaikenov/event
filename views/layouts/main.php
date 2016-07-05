<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\UserPreferencesWidget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::t('app', 'Welcome'). ' ('.(Yii::$app->user->isGuest ? Yii::t('app', 'Guest') : Yii::$app->user->identity->username).')',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $items = [
        ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']],
        ['label' => Yii::t('app', 'Manage site'), 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->can(['news', 'user'], false), 'items' => [
            ['label' => Yii::t('app', 'News'), 'url' => ['/news'], 'visible' => Yii::$app->user->can('news')],
            ['label' => Yii::t('app', 'Users'), 'url' => ['/user'], 'visible' => Yii::$app->user->can('user')],
            ['label' => Yii::t('app', 'Notification'), 'url' => ['/notification'], 'visible' => Yii::$app->user->can('notification')],
            ['label' => Yii::t('app', 'Notification Types'), 'url' => ['/notification-type'], 'visible' => Yii::$app->user->can('notification-type')],
        ]],
        ['label' => Yii::t('app', 'Manege access'), 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin(), 'items' => [
            ['label' => Yii::t('app', 'Roles'), 'url' => ['/permit/access/role'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()],
            ['label' => Yii::t('app', 'Permissions'), 'url' => ['/permit/access/permission'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()]
        ]]
    ];

    $items[] = ['label' => Html::tag('i', '', ['class' => 'glyphicon glyphicon-envelope']) . ' ' . (!Yii::$app->user->isGuest ? Html::tag('span', Yii::$app->user->identity->newNoticeCount, ['class' => 'badge']) : ''), 'url' => ['/notice'], 'visible' => !Yii::$app->user->isGuest];
    $items[] = ['label' => Html::tag('i', '', ['class' => 'glyphicon glyphicon-cog', 'data-toggle' => 'modal', 'data-target' => '#preference']), 'url' => '#', 'visible' => !Yii::$app->user->isGuest];

    if (Yii::$app->user->isGuest) {
        $items[] = ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']];
    } else {
        $items[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post', ['class' => 'navbar-form'])
            . Html::submitButton(
                Html::tag('i', '', ['class' => 'glyphicon glyphicon-log-out']),
                ['class' => 'btn btn-link']
            )
            . Html::endForm()
            . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $items,
        'encodeLabels' => false
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<?php if (!Yii::$app->user->isGuest): ?>
    <?php Modal::begin([
        'id' => 'preference',
        'header' => '<h2>' . Yii::t('app', 'Settings') . '</h2>',
    ]);
    echo UserPreferencesWidget::widget();
    Modal::end();
    ?>
<?php endif; ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
