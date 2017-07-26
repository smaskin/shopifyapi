<?php

namespace app\modules\storage;

/**
 * Storage module
 */
class Module extends \yii\base\Module
{
    public $apiKey;
    public $scopes;
    public $sharedSecret;
    public $appName;
    public $webhookUrls = [];

    public function init()
    {
        parent::init();
        $this->modules = [
            'product' => \app\modules\storage\modules\product\Module::className()
        ];
    }
}
