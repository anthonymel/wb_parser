<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use backend\models\forms\UserTextUtilForm;

class FileController extends Controller
{
    public function actionProcess($delimiter, $mode)
    {
        $form = new UserTextUtilForm();
        $form->delimiter = $delimiter;
        $form->mode = $mode;

        if ($form->process()) {
            $response = $form->getSuccessResponse();
        } else {
            $response = $form->getFirstErrors()[0];
        }

        echo $response;
    }
}
