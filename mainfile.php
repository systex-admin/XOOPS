<?php
/**
 * XOOPS main configuration file
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 */

$http = 'http://';
if (!empty($_SERVER['HTTPS'])) {
    $http = ($_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
}

if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
    $http = $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://';
}

// if ($_SERVER['REQUEST_URI'] != '/' and strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === false) {
//     // die(var_dump($_SERVER));
//     header("location: {$http}{$_SERVER["HTTP_HOST"]}");
//     exit;
// }
//$port = $_SERVER['SERVER_PORT'] == 80 ? '' : ":{$_SERVER['SERVER_PORT']}";

/*
不使用 $port
以下為 docker nginx  phpinfo() 的情形，server_port ，但 docker 外部轉 port 為 8080
$_SERVER['HTTP_HOST']    localhost:8080
$_SERVER['REDIRECT_STATUS']    200
$_SERVER['SERVER_NAME']    localhost
$_SERVER['SERVER_PORT']    80

 */

//自動取得網址
if (!function_exists('get_xoops_url')) {
    function get_xoops_url()
    {
        //global $http, $port;
        global $http;
        if (!isset($_SESSION['url'])) {

            //$u = parse_url($http . $_SERVER["HTTP_HOST"] . $port . $_SERVER['REQUEST_URI']);
            $u = parse_url($http . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI']);
            /*
            $_SERVER["HTTP_HOST"] 本身就會有 port 資料 ，如 localhost:8080
            如果是正常 port 80 443 等，網址本身就無須再多加 port 號

             */
            if (!empty($u['path']) and preg_match('/\/modules/', $u['path'])) {
                $XMUrl = explode("/modules", $u['path']);
            } elseif (!empty($u['path']) and preg_match('/\/themes/', $u['path'])) {
                $XMUrl = explode("/themes", $u['path']);
            } elseif (!empty($u['path']) and preg_match('/\/upgrade/', $u['path'])) {
                $XMUrl = explode("/upgrade", $u['path']);
            } elseif (!empty($u['path']) and preg_match('/\/include/', $u['path'])) {
                $XMUrl = explode("/include", $u['path']);
            } elseif (!empty($u['path']) and preg_match('/.php/', $u['path'])) {
                $XMUrl[0] = dirname($u['path']);
            } elseif (!empty($u['path'])) {
                $XMUrl[0] = $u['path'];
            } else {
                $XMUrl[0] = "";
            }

            $my_url = str_replace('\\', '/', $XMUrl['0']);
            if (substr($my_url, -1) == '/') {
                $my_url = substr($my_url, 0, -1);
            }

            $port = isset($u['port']) ? ":{$u['port']}" : '';
            /*
            如果有切出 port 號，在前面加入 : 號，傳出網址

             */

            $url = "{$u['scheme']}://{$u['host']}$port{$my_url}";

            return $url;
        } else {
            return $_SESSION['url'];
        }
    }
}

//自動取得實體位置
if (!function_exists('get_xoops_path')) {
    function get_xoops_path()
    {
        if (!isset($_SESSION['root_path'])) {
            $SCRIPT_FILENAME = str_replace('\\', '/', $_SERVER["SCRIPT_FILENAME"]);
            if (preg_match('/\/modules/', $SCRIPT_FILENAME)) {
                $XMPath = explode("/modules", $SCRIPT_FILENAME);
                $root_path = $XMPath[0];
            } elseif (preg_match('/\/themes/', $SCRIPT_FILENAME)) {
                $XMPath = explode("/themes", $SCRIPT_FILENAME);
                $root_path = $XMPath[0];
            } elseif (preg_match('/\/upgrade/', $SCRIPT_FILENAME)) {
                $XMPath = explode("/upgrade", $SCRIPT_FILENAME);
                $root_path = $XMPath[0];
            } elseif (preg_match('/\/include/', $SCRIPT_FILENAME)) {
                $XMPath = explode("/include", $SCRIPT_FILENAME);
                $root_path = $XMPath[0];
            } else {
                $root_path = dirname($SCRIPT_FILENAME);
            }
            // echo '-- no root_path session';
            return $root_path;
        } else {
            // echo '-- yes root_path session';
            return $_SESSION['root_path'];
        }
    }
}

$url = get_xoops_url(); //可用ip或網址，改完請清除 /xoops_data/caches/smarty_compile 所有快取
$root_path = str_replace('\\', '/', get_xoops_path());
$xoop_up_path = str_replace('\\', '/', dirname($root_path));

if (!defined('XOOPS_MAINFILE_INCLUDED')) {
    define('XOOPS_MAINFILE_INCLUDED', 1);

    // XOOPS Physical Paths

    // Physical path to the XOOPS documents (served) directory WITHOUT trailing slash
    // define('XOOPS_ROOT_PATH', '/var/www/html');
    define('XOOPS_ROOT_PATH', $root_path);

    // For forward compatibility
    // Physical path to the XOOPS library directory WITHOUT trailing slash
    // define('XOOPS_PATH', '/var/www/xoops_lib');
    define('XOOPS_PATH', "{$xoop_up_path}/xoops_lib");

    // Physical path to the XOOPS datafiles (writable) directory WITHOUT trailing slash
    // define('XOOPS_VAR_PATH', '/var/www/xoops_data');
    define('XOOPS_VAR_PATH', "{$xoop_up_path}/xoops_data");

    // Alias of XOOPS_PATH, for compatibility, temporary solution
    // URL Association for SSL and Protocol Compatibility
    // $http = 'http://';
    // if (!empty($_SERVER['HTTPS'])) {
    //     $http = ($_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    // }
    // define('XOOPS_PROT', $http);
    define('XOOPS_TRUST_PATH', XOOPS_PATH);

    // URL Association for SSL and Protocol Compatibility
    //$http = 'http://';
    //if (!empty($_SERVER['HTTPS'])) {
    //    $http = ($_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    //}
    define('XOOPS_PROT', $http);

    // XOOPS Virtual Path (URL)
    // Virtual path to your main XOOPS directory WITHOUT trailing slash
    // Example: define('XOOPS_URL', 'http://{YOUR_URL}');
    // define('XOOPS_URL', 'http://{YOUR_URL}');
    define('XOOPS_URL', $url);

    // XOOPS Cookie Domain to specify when creating cookies. May be blank (i.e. for IP address host),
    // full host from XOOPS_URL (i.e. www.example.com) or just the registered domain (i.e. example.com)
    // to share cookies across multiple subdomains (i.e. www.example.com and blog.example.com)
    // define('XOOPS_COOKIE_DOMAIN', '');
    define('XOOPS_COOKIE_DOMAIN', $_SERVER["HTTP_HOST"]);

    // Shall be handled later, don't forget!
    define('XOOPS_CHECK_PATH', 0);
    // Protect against external scripts execution if safe mode is not enabled
    if (XOOPS_CHECK_PATH && !@ini_get('safe_mode')) {
        if (function_exists('debug_backtrace')) {
            $xoopsScriptPath = debug_backtrace();
            if (!count($xoopsScriptPath)) {
                die('XOOPS path check: this file cannot be requested directly');
            }
            $xoopsScriptPath = $xoopsScriptPath[0]['file'];
        } else {
            $xoopsScriptPath = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];
        }
        if (DIRECTORY_SEPARATOR !== '/') {
            // IIS6 may double the \ chars
            $xoopsScriptPath = str_replace(strpos($xoopsScriptPath, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $xoopsScriptPath);
        }
        if (strcasecmp(substr($xoopsScriptPath, 0, strlen(XOOPS_ROOT_PATH)), str_replace(DIRECTORY_SEPARATOR, '/', XOOPS_ROOT_PATH))) {
            exit('XOOPS path check: Script is not inside XOOPS_ROOT_PATH and cannot run.');
        }
    }

    // Secure file
    require XOOPS_VAR_PATH . '/data/secure.php';

    define('XOOPS_GROUP_ADMIN', '1');
    define('XOOPS_GROUP_USERS', '2');
    define('XOOPS_GROUP_ANONYMOUS', '3');

    //輕鬆架安裝檢測
    if (file_exists(XOOPS_VAR_PATH . '/data/install_chk.php')) {
        include XOOPS_VAR_PATH . '/data/install_chk.php';
    }
    if (file_exists('/var/www/html/modules/tn_xoops/save_log.php')) {
        include '/var/www/html/modules/tn_xoops/save_log.php';
    }

    if (!isset($xoopsOption['nocommon']) && XOOPS_ROOT_PATH != '') {
        include XOOPS_ROOT_PATH . '/include/common.php';
    }

    if (!isset($_SESSION['url'])) {
        $_SESSION['url'] = $url;
    }
    if (!isset($_SESSION['root_path'])) {
        $_SESSION['root_path'] = $root_path;
    }
}
