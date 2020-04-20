<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AppleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Яблочки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apple-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Нагенерировать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'appleId',
            'color',
            'volume',
            [
                'attribute' => 'status',
                'value' => function (/** @var \backend\models\Apple $model */ $model) {
                    return \backend\models\Apple::statusLabels()[$model->status];
                },
            ],
            [
                'attribute' => 'droppedAt',
                'value' => function (/** @var \backend\models\Apple $model */ $model) {
                    return !empty($model->droppedAt) ? Yii::$app->formatter->asDatetime($model->droppedAt) : 'Пока не упало';
                },
            ],
            'createdAt:datetime',
            'updatedAt:datetime',

            [
                'class' => 'yii\grid\ActionColumn',

                'buttons' => [
                    'drop' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-tree-deciduous"></span>',
                            ['drop', 'appleId' => $key, 'referrer' => true],
                            ['title' => \Yii::t('yii', 'Уронить'),
                                'data' => [
                                    'confirm' => 'Вы уверены, что хотите уронить яблоко?',
                                    'method' => 'post',
                                ],
                            ]);
                    },
                    'eatMore' => function ($url, $model, $key) {
                        return Html::a('<div class="col-xm-2"><span class="glyphicon glyphicon-apple"></span></div>',
                            ['eat', 'appleId' => $key, 'percent' => 50, 'referrer' => true],
                            ['title' => \Yii::t('yii', 'Большой кусь'),
                                'data' => [
                                    'confirm' => 'Вы уверены, что хотите октусить яблоко?',
                                    'method' => 'post',
                                ],
                            ]);
                    },
                    'eatLitle' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-apple"></span>',
                            ['eat', 'appleId' => $key, 'percent' => 25, 'referrer' => true],
                            ['title' => \Yii::t('yii', 'Маленький кусь'),
                                'data' => [
                                    'confirm' => 'Вы уверены, что хотите октусить яблоко?',
                                    'method' => 'post',
                                ],
                            ]);
                    },
                ],
                'template'=>'{eatLitle} {eatMore} {drop} {delete}'
            ],
        ],
    ]); ?>
</div>
