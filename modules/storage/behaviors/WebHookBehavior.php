<?php

namespace app\modules\storage\behaviors;

use app\modules\storage\models\Shop;
use app\modules\storage\Module;
use Yii;
use yii\base\ActionFilter;
use yii\helpers\Json;
use yii\web\Controller;

class WebHookBehavior extends ActionFilter
{
    /**
     * @var Controller
     */
    public $owner;

    /**
     * @var array
     */
    private $_data;

    /**
     * @var Shop
     */
    public $_shop;

    public function beforeAction($action)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('storage');
        $this->checkRequest(Yii::$app->request->headers['x-shopify-hmac-sha256'], $module->sharedSecret);
        $this->setData();
        $this->setShop();
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        return \Yii::$app->response->isOk;
    }

    public function getWebhookData()
    {
        return $this->_data;
    }

    public function getShopModel()
    {
        return $this->_shop;
    }

    private function checkRequest($hmac, $sharedSecret)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', file_get_contents('php://input'), $sharedSecret, true));
        return !hash_equals($hmac, $calculated_hmac);
    }

    private function setData()
    {
        $this->_data = Json::decode(Yii::$app->request->getRawBody(), false);
    }

    private function setShop()
    {
        $this->_shop = Shop::findOne(['shop' => Yii::$app->request->headers['x-shopify-shop-domain']]);
    }
}