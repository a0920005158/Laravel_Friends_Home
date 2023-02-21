<?php

namespace App\Http\Controllers;

header('Access-Control-Allow-Origin: *'); // 讓vue rwd可以跨網域呼叫

include_once dirname(dirname(dirname(__FILE__))) . '/_Sys/config_redis.php';
include_once dirname(dirname(dirname(__FILE__))) . '/_Pub/enDecryption.php';

use App\Models\member;
use App\Models\permission;
use App\Models\permission_member;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class loginController extends Controller
{
    public function Run(Request $request)
    {
        $acc = $request->get('acc');
        $password = $request->get('password');
        $random = "";
        if (paramCheck($acc) && paramCheck($password)) {
            $encryptionPassword = encryption($password . substr($acc, -3));
            $userData = accAutGetUserData($acc, $encryptionPassword);
            if ($userData != null) {
                $random = encryption($userData['mid'] . $userData['name'] . time());
                redisSetUserData($random, $userData);
                // $permission = getPermission($userData['type']);
                setcookie("random", $random);
                // $this->urlRedir('https://ccf.bllin.net/UI/admin.html#/lobby/');
                // $midTable = getMidTable();
                $response = array();
                $response['callState'] = 0;
                $response['errorMsg'] = "";
                // $response['response'] = array("userdate" => $userData, "permission" => $permission, "midTable" => $midTable);
                $response['random'] = $random;
                return $response;
            } else {
                errorCall(0, $random);
            }
        } else {
            errorCall(1, $random);
        }
    }

    function urlRedir($url)
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $url);
    }

}

function errorCall($code, $random)
{
    switch ($code) {
        case 1: //setUserData 失敗
            $msg = 'SUD1';
            break;
        case 2: //驗證 失敗
            $msg = 'EP2';
            break;
        case 3: //Run 失敗
            $msg = 'RUN3';
            break;
        case 4: //Random Update 失敗
            $msg = 'RU4';
            break;
        case 5: //無權限
            $msg = 'P5';
            break;
    }
    $errorMsg = array();
    $errorMsg['callState'] = 1;
    $errorMsg['errorMsg'] = $msg;
    $errorMsg['response'] = array();
    $errorMsg['random'] = $random;
    return $errorMsg;
    exit();
}

function getPermission($type)
{
    $permission = permission_member::firstWhere('m_type', $type);
    $result = array();
    if ($permission != null) {
        $chiA = explode(',', $permission['permission']);
        $permissionObj = new permission();
        $permissionD = $permissionObj->whereIn('id', $chiA)->get();

        $pp = array();
        print_r($permissionD);
        if (count($permissionD) > 0) {
            foreach ($permissionD as $key => $value) {
                // echo $value['child_id'];
                // $child_id = $value['child_id'];
                // if (isset($value['child_id'])) {
                //     $child = array_filter($permissionD, function ($elem) use ($child_id) {
                //         return in_array($elem[0], explode(',', $child_id));
                //     });
                //     $childrenA = array();
                //     foreach ($child as $chvalue) {
                //         array_push($childrenA, array("name" => $chvalue[3], "path" => $chvalue[4]));
                //     }
                //     array_push($result, array("name" => $value[3], "icon" => $value[1], "children" => $childrenA));
                // }

            }
        }

        print_r($result);
        return $result;
    } else {
        return array();
    }
}

function redisSetUserData($random, $userData)
{
    $redis = getRedis();
    $redis->hset("userData:" . $random, 'mid', $userData['mid']);
    $redis->hset("userData:" . $random, 'acc', $userData['acc']);
    $redis->hset("userData:" . $random, 'job', $userData['job']);
    $redis->hset("userData:" . $random, 'joinDate', $userData['joinDate']);
    $redis->hset("userData:" . $random, 'type', $userData['type']);
    $redis->hset("userData:" . $random, 'line', $userData['line']);
    $redis->hset("userData:" . $random, 'phone', $userData['phone']);
    $redis->hset("userData:" . $random, 'birthDay', $userData['birthDay']);
    $redis->hset("userData:" . $random, 'name', $userData['name']);
    $redis->hset("userData:" . $random, 'userId', $userData['line_user_id']);

    $previousR = $redis->hget("userCall:" . $userData['line_user_id'], "r");
    if ($previousR != null) {
        $redis->del("userCall:" . $userData['line_user_id']);
        $redis->del("userData:" . $previousR);
    }

    $redis->hset("userCall:" . $userData['line_user_id'], "r", $random);
    $redis->hset("userCall:" . $userData['line_user_id'], "dateTime", date("Y-m-d H:i:s"));
}

function accAutGetUserData($acc, $pass)
{
    if ($acc == null || $acc == "" || $pass == null || $pass == "") {
        return null;
    }

    $sql = member::firstWhere('acc', $acc)::firstWhere('password', $pass);
    return $sql;
}

function paramCheck($str)
{
    $len_OLD = mb_strlen($str, 'utf-8');
    $tmp = strtolower($str);

    $pregStr = 'select|insert|update|delete|union|into|load_file|outfile|script|drop|http|truncate|having|shutdown';
    $w = explode('|', $pregStr);

    for ($i = 0; $i < sizeof($w); $i++) {
        $tmp = str_replace($w[$i], '', $tmp);
    }

    $len_NEW = mb_strlen($tmp, 'utf-8');

    if ($len_OLD != $len_NEW) {
        return false;
    }

    return true;
}
