<?php
/**
 * PROJECT : light-sdk
 * FILE    : wechat.class.php
 * TIME    : 2019/6/3 21:40
 * AUTHOR  : Younger Shen
 * EMAIL   : younger.x.shen@gmail.com
 * PHONE   : 13811754531
 * WECHAT  : 13811754531
 * WEBSIT  : https://www.punkcoder.cn
 */

namespace LightSDK;

class Wechat
{
    private $debug;
    private $appId;
    private $appSecret;
    private $token;
    private $encryptType;
    private $encodingAeskey;
    private $apiUrl;

    public function __construct($appId, $appSecret, $token, $encryptType, $encodingAeskey, $apiUrl='api.weixin.qq.com', $debug=false)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->token = $token;
        $this->encryptType = $encryptType;
        $this->encodingAeskey = $encodingAeskey;
        $this->apiUrl = $apiUrl;
        $this->debug = $debug;
    }
}