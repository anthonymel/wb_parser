<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use console\models\UserTextUtilForm;

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
            $response = $form->getFirstErrors();
        }

        echo $response;
    }
}
