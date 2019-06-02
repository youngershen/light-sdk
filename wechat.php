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

define('LIGHT_SDK_WECHAT_DEBUG', true); // 启用调试模式
define('LIGHT_SDK_WECHAT_APPID', 'wxcd18413123e1fff5'); // APPID 由微信公众平台获取
define('LIGHT_SDK_WECHAT_SECRET', 'a5435a82a733b4fbaae60a3ab901f403'); // APP Secret 由微信公众平台获取
define('LIGHT_SDK_WECHAT_TOKEN', ''); // Token 由微信公众平台自行填写
define('LIGHT_SDK_WECHAT_ENCRYPT_TYPE', 0); // 0 => 明文, 1 => 兼容, 2 => 安全
define('LIGHT_SDK_WECHAT_ENCODING_AESKEY', ''); // 若消息加密模式为 2, 则必须根据微信公众平台中所填内容来填写此项
define('LIGHT_SDK_WECHAT_API_URL', 'api.weixin.qq.com'); // 微信接口域名
define('LIGHT_SDK_WECHAT_APICLIENT_CERT_PATH', null); // 微信支付 CERT 证书路径
define('LIGHT_SDK_WECHAT_APICLIENT_KEY_PATH', null); // 微信支付 KEY 路径

define('LIGHT_SDK_WECHAT_VERSION', '0.1a');
define('LIGHT_SDK_BASE_DIR', dirname(__FILE__));
define('LIGHT_SDK_LOG', LIGHT_SDK_BASE_DIR . DIRECTORY_SEPARATOR . 'debug.log');

/**
 * @param array $curl_options [可选]
 * curl 的其他参数都可以传入，方便调用
 * @return bool|string
 * 请求成功则返回字符串，否则返回 false， 默认开启 CURLOPT_RETURNTRANSFER
 */
function light_sdk_wechat_util_curl($curl_options=null)
{
    $ch = curl_init();
    $curl_options = light_sdk_wechat_util_curl_options($curl_options);
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
function light_sdk_wechat_util_curl_options($options)
{
    $default = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_PORT => 443,
        CURLOPT_CONNECTTIMEOUT => 6,
        CURLOPT_TIMEOUT => 8
    ];

    return array_replace($default, $options);
}

function light_sdk_wechat_util_debug($message)
{
    if(LIGHT_SDK_WECHAT_DEBUG)
    {
        light_sdk_wechat_util_log($message);
    }
}

function light_sdk_wechat_util_log($message)
{
    $message = $message . PHP_EOL;
    error_log($message, 3, LIGHT_SDK_LOG);
}

/**
 * @param string $api
 * 微信接口的 end point
 * @param array $params [optional]
 * 可选的 query 参数 方便一些 get 请求
 * @return string
 * 返回拼装好之后的 URL
 */
function light_sdk_wechat_util_get_api_url($api, $params=null)
{
    $format = '%1$s://%2$s%3$s';
    $url = sprintf($format, 'https', LIGHT_SDK_WECHAT_API_URL, $api);

    if($params)
    {
        $query = http_build_query($params);
        $url =  $url . '?' . $query;
    }

    return $url;
}

function light_sdk_wechat_util_check_signature($signature, $timestamp, $nonce)
{
    $data = [LIGHT_SDK_WECHAT_TOKEN, $timestamp, $nonce];
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
function light_sdk_wechat_api_get_callback_ip_list($access_token)
{
    $params = [
        'access_token' => $access_token
    ];

    $url = light_sdk_wechat_util_get_api_url('/cgi-bin/getcallbackip', $params);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => true,
    ];

    $response = light_sdk_wechat_util_curl($options);
    $json = json_decode($response, true);
    return $json;
}

/**
 * 获取微信 access_token
 * @return bool|string
 * 成功色返回字符串，失败则返回 false
 * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140183
 */
function light_sdk_wechat_api_get_access_token()
{
    $params = [
        'grant_type' => 'client_credential',
        'appid' => LIGHT_SDK_WECHAT_APPID,
        'secret' => LIGHT_SDK_WECHAT_SECRET
    ];

    $url = light_sdk_wechat_util_get_api_url('/cgi-bin/token', $params);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => true,
    ];

    $response = light_sdk_wechat_util_curl($options);
    $json = json_decode($response, true);
    return $json;
}

/**
 * @param array $payload
 * 自定义菜单数组，具体格式参见微信官方的文档
 * @param string $access_token
 * 有效的 access_token
 * @return string|bool
 * 调用成功则返回微信返回的字符串，失败则返回 false
 * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013
 * 该接口用于创建微信公众号菜单，详情参见微信官方文档
 */
function light_sdk_wechat_api_menu_create($access_token, $payload)
{
    $url = light_sdk_wechat_util_get_api_url('/cgi-bin/menu/create', ['access_token' => $access_token]);
    $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);

    light_sdk_wechat_util_debug($url);
    light_sdk_wechat_util_debug($payload);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload
    ];

    $response = light_sdk_wechat_util_curl($options);
    light_sdk_wechat_util_debug($response);
    $json = json_decode($response, true);
    return $json;
}

/**
 * @param string $access_token
 * 有效的 access_token
 * @return mixed
 * 调用成功则返回具体的菜单信息，否则返回 false
 * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141014
 * 该函数用于获取公众号的菜单，详情参见官方文档
 */
function light_sdk_wechat_api_menu_get($access_token)
{
    $url = light_sdk_wechat_util_get_api_url('/cgi-bin/menu/get', ['access_token' => $access_token]);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => true,
    ];

    $response = light_sdk_wechat_util_curl($options);
    light_sdk_wechat_util_debug($response);
    $json = json_decode($response, true);
    return $json;
}

/**
 * @param string $access_token
 * 有效的 access_token
 * @return mixed
 * 调用成功则返回删除成功的提示信息，否则返回 false
 * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141015
 * 该函数用于删除公众号菜单，详情参见官方文档
 */
function light_sdk_wechat_api_menu_delete($access_token)
{
    $url = light_sdk_wechat_util_get_api_url('/cgi-bin/menu/delete', ['access_token' => $access_token]);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => true,
    ];

    $response = light_sdk_wechat_util_curl($options);
    light_sdk_wechat_util_debug($response);
    $json = json_decode($response, true);
    return $json;
}


/**
 * @param string $access_token
 * 有效的 acess_token
 * @param array $payload
 * 菜单创建详情， 参见官方文档
 * @return mixed
 * 正确时的返回JSON数据包如下，错误时的返回码请见接口返回码说明。
 * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455782296
 * 该函数用于创建个性化菜单， 详情参见官方文档
 */
function light_sdk_wechat_api_menu_addconditional($access_token, $payload)
{
    $url = light_sdk_wechat_util_get_api_url('/cgi-bin/menu/addconditional', ['access_token' => $access_token]);
    $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);

    light_sdk_wechat_util_debug($url);
    light_sdk_wechat_util_debug($payload);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload
    ];

    $response = light_sdk_wechat_util_curl($options);
    light_sdk_wechat_util_debug($response);
    $json = json_decode($response, true);
    return $json;
}

/**
 * @param string $access_token
 * 有效的 access_token
 * @param string $menu_id
 * 要删除的 menu id
 * @return mixed
 * 返回参见官方文档
 * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455782296
 * 改函数用于删除个性化菜单, 详情参见官方文档
 */
function light_sdk_wechat_api_menu_delconditional($access_token, $menu_id)
{
    $url = light_sdk_wechat_util_get_api_url('/cgi-bin/menu/delconditional', ['access_token' => $access_token]);
    $payload = ['menuid' => $menu_id];

    $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);

    light_sdk_wechat_util_debug($url);
    light_sdk_wechat_util_debug($payload);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload
    ];

    $response = light_sdk_wechat_util_curl($options);
    light_sdk_wechat_util_debug($response);
    $json = json_decode($response, true);
    return $json;
}


/**
 * @param string $access_token
 * 可用的 access_token
 * @param string $user_id
 * 用户 user id
 * @return mixed
 * 返回参见文档
 * @Link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455782296
 * 该函数用户测试匹配个性化菜单，详情参见官方文档
 */
function light_sdk_wechat_api_menu_trymatch($access_token, $user_id)
{
    $url = light_sdk_wechat_util_get_api_url('/cgi-bin/menu/trymatch', ['access_token' => $access_token]);
    $payload = ['user_id' => $user_id];

    $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);

    light_sdk_wechat_util_debug($url);
    light_sdk_wechat_util_debug($payload);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload
    ];

    $response = light_sdk_wechat_util_curl($options);
    light_sdk_wechat_util_debug($response);
    $json = json_decode($response, true);
    return $json;
}


/**
 * @param string $access_token
 * 有效的 access_token
 * @return mixed
 * 调用成功则返回自定义菜单的 json 数据，否则返回 false
 * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1434698695
 * 该函数用于获取自定义菜单信息，详情参见官方文档
 */
function light_sdk_wechat_api_get_current_selfmenu_info($access_token)
{
    $url = light_sdk_wechat_util_get_api_url('/cgi-bin/get_current_selfmenu_info', ['access_token' => $access_token]);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => true,
    ];

    $response = light_sdk_wechat_util_curl($options);
    light_sdk_wechat_util_debug($response);
    $json = json_decode($response, true);
    return $json;
    
}
