<?php

namespace app\modules\storage\controllers;

use app\modules\storage\behaviors\WebHookBehavior;
use Yii;
use yii\web\Controller;

/**
 * @mixin WebHookBehavior
 */
class HookController extends Controller
{
    public function behaviors()
    {
        return ['webHook' => WebHookBehavior::classname()];
    }

    public function actionUninstall()
    {
        $shop = $this->getShopModel();
        Yii::$app->user->logout();
        return $shop->delete();
    }
}
