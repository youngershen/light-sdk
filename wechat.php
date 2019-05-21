<?php
/**
 * PROJECT : light-sdk
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
define('WECHAT_TOKEN', ''); // Token 由微信公众平台自行填写
define('WECHAT_ENCRYPT_TYPE', 0); // 0 => 明文, 1 => 兼容, 2 => 安全
define('WECHAT_ENCODING_AESKEY', ''); // 若消息加密模式为 2, 则必须根据微信公众平台中所填内容来填写此项
define('WECHAT_API_URL', 'api.weixin.qq.com'); // 微信接口域名
define('WECHAT_APICLIENT_CERT_PATH', null); // 微信支付 CERT 证书路径
define('WECHAT_APICLIENT_KEY_PATH', null); // 微信支付 KEY 路径

/**
 * @param array $args
 * 该参数中必须包含 url 项， query 项可以为空
 * @param array $curl_opts
 * 该参数用来设置 curl 选项，依需求添加即可
 * @return bool|string
 * 若请求成功则返回字符串，失败怎返回 false
 */
function wechat_util_http_get($args, $curl_opts=null)
{
    $url = $args['url'];
    $port = 80;

    if(array_key_exists('query', $args))
    {
        $params = http_build_query($args['query']);
        $url .= '?' . $params;
    }

    if(preg_match('/https/', $url, $match))
    {
        $port = 443;
    }

    $opts = [
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_PORT => $port
    ];

    if($curl_opts)
    {
        $opts = array_merge($opts, $curl_opts);
    }

    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function wechat_util_http_post($args, $curl_opts=null)
{
    $url = $args['url'];
    $port = 80;
    $payload = null;

    if(preg_match('/https/', $url, $match))
    {
        $port = 443;
    }

    if(preg_match(''))

    if(key_exists('payload', $args))
    {
        $payload = http_build_query($payload);
    }

    $opts = [
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => false,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_PORT => $port
    ];

    if($curl_opts)
    {
        $opts = array_merge($opts, $curl_opts);
    }

    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function wechat_util_debug()
{

}

function wechat_util_log()
{

}

function wecaht_util_get_api_url($api)
{
    $format = '%1$s://%2$s%3$s';
    $url = sprintf($format, 'https', WECHAT_API_URL, $api);
    return $url;
}

function wechat_util_check_signature($signature, $timestamp, $nonce)
{
    $data = [WECHAT_TOKEN, $timestamp, $nonce];
    sort($data, SORT_STRING);
    $s = sha1(implode($data));
    return $s == $signature;
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


echo(wechat_util_http_post(['url' => 'http://localhost:8080']));