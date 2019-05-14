<?php
/**
 * PROJECT : wechat
 * FILE    : wechat.php
 * TIME    : 2019/5/14 15:18
 * AUTHOR  : Younger Shen
 * EMAIL   : younger.x.shen@gmail.com
 * PHONE   : 13811754531
 * WECHAT  : 13811754531
 * WEBSIT  : https://www.punkcoder.cn
 * DESCRIPTION : 一个极简的全功能 PHP 微信开发包， 整个包只有一个文件，兼容老旧 PHP 项目，使用方便，快捷简单。
 */

define('WECHAT_DEBUG', true); // 启用调试模式
define('WECHAT_APPID', ''); // APPID 由微信公众平台获取
define('WECHAT_SECRET', ''); // APP Secret 由微信公众平台获取
define('WECHAT_ENCRYPT_TYPE', 0); // 0 => 明文, 1 => 兼容, 2 => 安全
define('WECHAT_ENCODING_AESKEY', ''); // 若消息加密模式为 2, 则必须根据微信公众平台中所填内容来填写此项
define('WECHAT_API_URL', 'api.weixin.qq.com'); // 微信接口域名
define('WECHAT_APICLIENT_CERT_PATH', null); // 微信支付 CERT 证书路径
define('WECHAT_APICLIENT_KEY_PATH', null); // 微信支付 KEY 路径

function wechat_util_debug()
{

}

function wechat_util_log()
{

}

function wechat_util_http_get($url, $params)
{
    return null;
}

function wechat_util_http_post()
{

}

function wecaht_util_get_api_url($api)
{
    $format = '%1$s://%2$s%3$s';
    $url = sprintf($format, 'https', WECHAT_API_URL, $api);
    return $url;
}

function wechat_api_get_callback_ip_list($access_token)
{

}

function wechat_api_get_access_token()
{
    $params = [
        'grant_type' => 'client_credential',
        'appid' => WECHAT_APPID,
        'secret' => WECHAT_SECRET
    ];

    $url = wecaht_util_get_api_url('/cgi-bin/token');
    $response = wechat_util_http_get($url, $params);
    return $response;
}

function wechat_api_micropay()
{

}

function wechat_api_jspay()
{

}

function wechat_api_nativepay()
{

}

function wechat_api_apppay()
{

}

function wechat_api_h5pay()
{

}

function wechat_api_apay()
{

}

function wechat_api_facepay()
{

}


