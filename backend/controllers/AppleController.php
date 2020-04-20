<?php

namespace backend\controllers;

use Yii;
use backend\models\Apple;
use backend\models\AppleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppleController implements the CRUD actions for Apple model.
 */
class AppleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Apple models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AppleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Apple model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // создаем от 0 до 10 яблок (случайных)
        $appleRandomCount = rand(0, 10);

        for ($counter = 0; $counter <= $appleRandomCount; $counter++) {
            /** @var Apple $apple */
            $apple = new Apple();
            if (!$apple->save(false)) {
            }
        }
        return $this->actionIndex();
    }

    public function actionEat($appleId, $percent)
    {
        $apple = Apple::findOne($appleId);
        if (empty($apple)) {
            Yii::$app->session->setFlash('error', 'Яблоко не найдено');
            return $this->actionIndex();
        }
        if (!$apple->isPossibleEat()) {
            Yii::$app->session->setFlash('error', 'Нельзя скушать');
            return $this->actionIndex();
        }
        if ($percent < 0) {
            Yii::$app->session->setFlash('error', 'Процент, введеный вами меньше нуля, либо не является целым числом');
            return $this->actionIndex();
        }

        Yii::$app->session->setFlash('success', 'Молодец, откусил');
        $apple->eat((int)$percent);
        return $this->actionIndex();
    }

    public function actionDrop($appleId)
    {
        $apple = Apple::findOne($appleId);
        if (empty($apple)) {
            Yii::$app->session->setFlash('error', 'Яблоко не найдено');
            return $this->actionIndex();
        }
        if ($apple->status != Apple::STATUS_ON_TREE) {
            Yii::$app->session->setFlash('error', 'Уже упало');
            return $this->actionIndex();
        }

        Yii::$app->session->setFlash('success', 'Яблоко упало');
        $apple->drop($apple);

        return $this->actionIndex();
    }

    /**
     * Updates an existing Apple model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->appleId]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Apple model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Apple model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Apple the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Apple::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
