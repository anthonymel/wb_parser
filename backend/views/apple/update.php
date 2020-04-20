<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Apple */

$this->title = 'Update Apple: ' . $model->appleId;
$this->params['breadcrumbs'][] = ['label' => 'Apples', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->appleId, 'url' => ['view', 'id' => $model->appleId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="apple-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
