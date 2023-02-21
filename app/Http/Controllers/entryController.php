<?php

namespace App\Http\Controllers;

header('Access-Control-Allow-Origin: *'); // 讓vue rwd可以跨網域呼叫

include_once dirname(dirname(dirname(__FILE__))) . '/_Sys/config_redis.php';
include_once dirname(dirname(dirname(__FILE__))) . '/_Pub/enDecryption.php';

use App\Models\permission_member;
use Illuminate\Http\Request;
use Log;

class entryController
{
    private $st;
    private $apiName;
    public function excute(Request $request, $apiName)
    {
        $this->st = $this->caclutime();
        $apiName = $apiName . 'Controller';
        $this->apiName = $apiName;
        $random = $request->get('random');
        $userData = $this->getUserData($random);
        $newRandom = "";
        if (isApiExist($apiName) && count($userData) > 0) {
            $newRandom = encryption($userData['mid'] . $userData['name'] . time());
            $redis = getRedis();
            $isUpdateRandomSuccess = $redis->rename("userData:" . $random, "userData:" . $newRandom);
            if ($isUpdateRandomSuccess) {
                $redis->hset("userCall:" . $userData['userId'], "r", $newRandom);
                $redis->hset("userCall:" . $userData['userId'], "dateTime", date("Y-m-d H:i:s"));
                $apiClass = 'Api\\' . $apiName;
                $apiObj = new $apiClass();
                $apiObj->setRandom($newRandom);
                $apiObj->setRequest($request);
                if (permissionVerification($userData['type'], $apiName)) {
                    if (!$apiObj->setUserData($userData)) {
                        return $this->errorCall(1, $newRandom);
                    }
                    $responseData = $apiObj->Run();
                    if ($responseData === null) {
                        return $this->errorCall(3, $newRandom);
                    } else {
                        return $this->responseCall($responseData, $newRandom);
                    }
                } else {
                    return $this->errorCall(5, $newRandom);
                }
            } else {
                return $this->errorCall(4, $newRandom);
            }
        } else {
            return $this->errorCall(2, $newRandom);
        }
    }

    public function getUserData($random)
    {
        $redis = getRedis();
        $redisR = $redis->hgetall("userData:" . $random);
        return $redisR;
    }

    function caclutime()
    {
        $time = explode(" ", microtime());
        $usec = (float)$time[0];
        $sec = (float)$time[1];
        return $sec + $usec;
    }

    function responseCall($data, $random)
    {
        $runTime = round($this->caclutime() - $this->st, 5);
        Log::info($this->apiName . '-' . date('Y-m-d H:i:s') . '-runTime: ' . $runTime);
        $responseData = array();
        $responseData['callState'] = 0;
        $responseData['errorMsg'] = "";
        $responseData['response'] = $data;
        $responseData['random'] = $random;
        return $responseData;
    }

    function errorCall($code, $random)
    {
        $runTime = round($this->caclutime() - $this->st, 5);
        Log::info($this->apiName . '-' . date('Y-m-d H:i:s') . '-runTime: ' . $runTime);
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
}

function permissionVerification($type, $apiName)
{
    if ($type == 1) {
        return true;
    }
    $redis = getRedis();
    $role_power = $redis->hget("role_power", $type);
    if ($role_power == null) {
        $sqlResult = permission_member::firstWhere('m_type', $type);
        $role_power = $sqlResult['callApi'];
        if ($role_power != null) {
            $redis->hset("role_power", $type, $role_power);
        }
    }

    $dataA = explode(',', $role_power);
    if (in_array(str_replace("Controller", "", $apiName), $dataA)) {
        return true;
    }

    return false;
}

//判斷api是否存在
function isApiExist($apiName)
{
    if (isset($apiName)) {
        if (file_exists(dirname(__FILE__) . '//api//' . $apiName . '.php')) {
            include_once dirname(__FILE__) . '//api//' . $apiName . '.php';
            if (class_exists('Api\\' . $apiName)) {
                return true;
            }
        }
    }
    return false;
}
