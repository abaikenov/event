<?php

namespace tests\codeception\unit\models;

use app\models\News;
use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;

class NewsTest extends TestCase
{
    use Specify;

    protected function tearDown()
    {
        $model = News::find()->where(['title' => 'Title'])->one();
        if(null != $model)
            $model->delete();
        parent::tearDown();
    }

    public function testCreateNews()
    {
        $model = new News();
        $model->attributes = [
            'title' => 'Title',
            'announce' => 'Announce',
            'text' => 'Text',
        ];

        $this->specify('news should contain correct data', function () use ($model) {
            expect('news must be validated', $model->validate())->true();
            expect('news must be saved', $model->save())->true();
        });
    }

}
