<?php
/**
 * PROJECT : light-sdk
 * FILE    : wechat.php
 * TIME    : 2019/5/14 15:18
 * AUTHOR  : Younger Shen
 * EMAIL   : younger.x.shen@gmail.com
 * PHONE   : 13811754531
 * WECHAT  : 13811754531
 * WEBSIT  : https://github.com/youngershen/light-sdk
 * LICENSE : https://opensource.org/licenses/MIT
 * REQUIREMENTS: PHP 5.4 及以上版本， 所需扩展组件 curl
 * DESCRIPTION : 一个极简的全功能 PHP 微信开发包， 整个包只有一个文件，兼容老旧 PHP 项目，使用方便，快捷简单。
 */

define('WECHAT_DEBUG', true); // 启用调试模式
define('WECHAT_APPID', 'wxcd18413123e1fff5'); // APPID 由微信公众平台获取
define('WECHAT_SECRET', 'a5435a82a733b4fbaae60a3ab901f403'); // APP Secret 由微信公众平台获取
define('WECHAT_TOKEN', ''); // Token 由微信公众平台自行填写
define('WECHAT_ENCRYPT_TYPE', 0); // 0 => 明文, 1 => 兼容, 2 => 安全
define('WECHAT_ENCODING_AESKEY', ''); // 若消息加密模式为 2, 则必须根据微信公众平台中所填内容来填写此项
define('WECHAT_API_URL', 'api.weixin.qq.com'); // 微信接口域名
define('WECHAT_APICLIENT_CERT_PATH', null); // 微信支付 CERT 证书路径
define('WECHAT_APICLIENT_KEY_PATH', null); // 微信支付 KEY 路径

/**
 * @param array $curl_options [可选]
 * curl 的其他参数都可以传入，方便调用
 * @return bool|string
 * 请求成功则返回字符串，否则返回 false， 默认开启 CURLOPT_RETURNTRANSFER
 */
function wechat_util_curl($curl_options=null)
{
    $ch = curl_init();
    $curl_options = wechat_util_curl_options($curl_options);
    curl_setopt_array($ch, $curl_options);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

/**
 * @param array $options
 * 需要合并的额外 curl 参数， 将于默认参数合并后返回新的参数数组
 * @return array
 * 新的参数数组
 */
function wechat_util_curl_options($options)
{
    $default = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_PORT => 443,
        CURLOPT_CONNECTTIMEOUT => 6,
        CURLOPT_TIMEOUT => 8
    ];

    return array_replace($default, $options);
}

function wechat_util_debug()
{

}

function wechat_util_log()
{

}


/**
 * @param string $api
 * 微信接口的 end point
 * @param array $params [optional]
 * 可选的 query 参数 方便一些 get 请求
 * @return string
 * 返回拼装好之后的 URL
 */
function wechat_util_get_api_url($api, $params=null)
{
    $format = '%1$s://%2$s%3$s';
    $url = sprintf($format, 'https', WECHAT_API_URL, $api);

    if($params)
    {
        $query = http_build_query($params);
        $url =  $url . '?' . $query;
    }

    return $url;
}

function wechat_util_check_signature($signature, $timestamp, $nonce)
{
    $data = [WECHAT_TOKEN, $timestamp, $nonce];
    sort($data, SORT_STRING);
    $s = sha1(implode($data));
    return $s == $signature;
}

/**
 * @param string $access_token
 * 可用的 access token
 * @return string|bool
 * 成功则返回 json 失败返回 false
 * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140187
 * 该方法用来获取微信服务器的 ip 地址, 详细参见 link
 */
function wechat_api_get_callback_ip_list($access_token)
{
    $params = [
        'access_token' => $access_token
    ];

    $url = wechat_util_get_api_url('/cgi-bin/getcallbackip', $params);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => true,
    ];

    $response = wechat_util_curl($options);
    $json = json_decode($response, true);
    return $json;
}


/**
 * 获取微信 access_token
 * @return bool|string
 * 成功色返回字符串，失败则返回 false
 * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140183
 */
function wechat_api_get_access_token()
{
    $params = [
        'grant_type' => 'client_credential',
        'appid' => WECHAT_APPID,
        'secret' => WECHAT_SECRET
    ];

    $url = wechat_util_get_api_url('/cgi-bin/token', $params);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => true,
    ];

    $response = wechat_util_curl($options);
    $json = json_decode($response, true);
    return $json;
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


$token = wechat_api_get_access_token();
$ip = wechat_api_get_callback_ip_list($token['access_token']);
var_dump($ip);