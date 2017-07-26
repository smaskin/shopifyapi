<?php

namespace app\modules\storage\controllers;

use app\modules\storage\models\Shop;
use Yii;
use yii\helpers\Url;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class DefaultController extends Controller
{
    /**
     * @var \app\modules\storage\Module
     */
    public $module;

    public function actionInstall()
    {
        $shop = Yii::$app->request->get('shop');
        $redirect_uri = Url::toRoute('auth', true);
        return Yii::$app->user->isGuest
            ? $this->redirect("https://{$shop}/admin/oauth/authorize?client_id={$this->module->apiKey}&scope={$this->module->scopes}&redirect_uri=$redirect_uri")
            : $this->redirect(['/storage/product/default/index']);
    }

    public function actionAuth()
    {
        $this->checkRequest(Yii::$app->request->get('hmac'));
        $shop = Yii::$app->request->get('shop');
        if (!($token = $this->getAccessToken($shop))) {
            Yii::error('Empty token');
        }
        if (!$this->saveToken($shop, $token)) {
            Yii::error('Save token error');
        }
        foreach ($this->module->webhookUrls as $topic => $url) {
            $this->sendWebHook($shop, $token, $topic, $url);
        }
        Yii::error(Yii::$app->user->loginByAccessToken($token));
        return $this->redirect("https://{$shop}/admin/apps/{$this->module->appName}");
    }

    private function saveToken($shop, $token)
    {
        $model = ($existed = Shop::findOne(['shop' => $shop]))
            ? $existed
            : new Shop(['shop' => $shop]);
        $model->token = $token;
        $model->timestamp = time();
        return $model->save();
    }

    private function getAccessToken($shop)
    {
        $httpClient = new Client();
        $response = $httpClient->createRequest()
            ->setMethod('post')
            ->setUrl("https://{$shop}/admin/oauth/access_token")
            ->setData([
                'client_id' => $this->module->apiKey,
                'client_secret' => $this->module->sharedSecret,
                'code' => Yii::$app->request->get('code')
            ])
            ->send();
        if (!$response->isOk) {
            throw new BadRequestHttpException('ShopifyAPI status code:' . $response->statusCode);
        }
        return $response->data['access_token'];
    }

    private function sendWebHook($shop, $token, $topic, $url)
    {
        $httpClient = new Client();
        $response = $httpClient->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('post')
            ->setUrl("https://{$shop}/admin/webhooks.json")
            ->addHeaders(['X-Shopify-Access-Token' => $token])
            ->setData(['webhook' => [
                'topic' => $topic,
                'address' => Url::toRoute($url, true),
                'format' => 'json'
            ]])
            ->send();
        Yii::error($response->data);
    }

    private function checkRequest($hmac)
    {
        $get = \Yii::$app->request->get();
        unset($get['hmac']);
        foreach ($get as $key => $val) $params[] = "$key=$val";
        sort($params);
        $calculated_hmac = hash_hmac('sha256', implode('&', $params), $this->module->sharedSecret);
        if (!hash_equals($hmac, $calculated_hmac)) {
            throw new BadRequestHttpException('Wrong hmac');
        }
    }
}
