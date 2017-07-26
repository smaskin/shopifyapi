<?php

namespace app\modules\storage\modules\product\controllers;

use app\modules\storage\modules\product\models\ProductSearch;
use Yii;
use yii\web\Controller;

/**
 */
class DefaultController extends Controller
{
    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
