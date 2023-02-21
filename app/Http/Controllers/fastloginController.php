<?php

namespace App\Http\Controllers;

header('Access-Control-Allow-Origin: *'); // 讓vue rwd可以跨網域呼叫

include_once dirname(dirname(dirname(__FILE__))) . '/_Sys/config_redis.php';
include_once dirname(dirname(dirname(__FILE__))) . '/_Pub/enDecryption.php';

use App\Models\member;
use App\Models\permission;
use App\Models\permission_member;
use Illuminate\Http\Request;

class fastloginController
{
    public function Run(Request $request)
    {
        $r = $request->get("r");
        $random = "";
        if (paramCheck($r)) {
            $userData = accAutGetUserData($r);
            if ($userData != null) {
                $random = encryption($userData['mid'] . $userData['name'] . time());
                $redis = getRedis();
                $isUpdateRandomSuccess = $redis->rename("userData:" . $r, "userData:" . $random);
                if ($isUpdateRandomSuccess) {
                    $redis->hset("userCall:" . $userData['userId'], "r", $random);
                    $redis->hset("userCall:" . $userData['userId'], "dateTime", date("Y-m-d H:i:s"));
                    $permission = getPermission($userData['type']);
                    $midTable = getMidTable();
                    $response = array();
                    $response['callState'] = 0;
                    $response['errorMsg'] = "";
                    $response['response'] = array("userdate" => $userData, "permission" => $permission, "midTable" => $midTable);
                    $response['random'] = $random;
                    return $response;
                }
            } else {
                return errorCall(0, $random);
            }
        } else {
            return errorCall(1, $random);
        }
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

    $previousR = $redis->hget("userCall:" . $userData['userId'], "r");
    if ($previousR != null) {
        $redis->del("userCall:" . $userData['userId']);
        $redis->del("userData:" . $previousR);
    }

    $redis->hset("userCall:" . $userData['userId'], "r", $random);
    $redis->hset("userCall:" . $userData['userId'], "dateTime", date("Y-m-d H:i:s"));
}

function getPermission($type)
{
    $permission_model = new permission();
    $permission = permission_member::firstWhere('m_type', $type);
    $result = array();
    if ($permission != null) {
        $chiA = explode(',', $permission['permission']);
        $permissionD = $permission_model->whereIn('id', $chiA)->get()->toArray();
        $pp = array();
        if (count($permissionD) > 0) {
            foreach ($permissionD as $key => $value) {
                $child_id = $value['child_id'];
                if (isset($value['child_id'])) {
                    $child = array_filter($permissionD, function ($elem) use ($child_id) {
                        return in_array($elem['id'], explode(',', $child_id));
                    });
                    $childrenA = array();
                    foreach ($child as $chvalue) {
                        array_push($childrenA, array("name" => $chvalue['title'], "path" => $chvalue['url']));
                    }
                    array_push($result, array("name" => $value['title'], "icon" => $value['icon'], "children" => $childrenA));
                }
            }
        }

        return $result;
    } else {
        return array();
    }
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

function getMidTable()
{
    $midTable = member::all();

    $newMidTable = array();
    foreach ($midTable as $key => $value) {
        if ($value['pass'] == 1)
            $newMidTable[$value['mid']] = $value['name'];
    }
    return $newMidTable;
}

function accAutGetUserData($r)
{
    $redis = getRedis();
    $redisR = $redis->hgetall("userData:" . $r);
    if ($redisR) {
        return $redisR;
    } else {
        return null;
    }
}

function errorCall($code, $random)
{
    switch ($code) {
        case 0: //帳密驗證 失敗
            $msg = '帳密驗證 失敗';
            break;
        case 1: //SQL injection
            $msg = 'LG1';
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
