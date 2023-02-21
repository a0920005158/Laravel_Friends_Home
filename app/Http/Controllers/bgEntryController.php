<?php
namespace App\Http\Controllers;

header('Access-Control-Allow-Origin: *'); // 讓vue rwd可以跨網域呼叫

include_once dirname(dirname(dirname(__FILE__))) . '/_Sys/config_redis.php';
include_once dirname(dirname(dirname(__FILE__))) . '/_Pub/enDecryption.php';

use Illuminate\Http\Request;
use Log;

class bgEntryController
{
    private $st;
    private $apiName;

    public function excute(Request $request, $apiName)
    {
        $this->st = $this->caclutime();
        $apiName = $apiName . 'Controller';
        $this->apiName = $apiName;

        if (isApiExist($apiName)) {
            $redis = getRedis();
            $apiClass = 'Bg\\Api\\' . $apiName;
            $apiObj = new $apiClass();
            $apiObj->setRequest($request);
            $responseData = $apiObj->Run();
            if ($responseData !== null) {
                return $this->responseCall($responseData);
            } else {
                return $this->errorCall(0);
            }
        } else {
            return $this->errorCall(0);
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

    function errorCall($code)
    {
        $runTime = round($this->caclutime() - $this->st, 5);
        Log::info('bg/'.$this->apiName . '-' . date('Y-m-d H:i:s') . '-runTime: ' . $runTime);
        switch ($code) {
            case 0: //執行失敗
                $msg = 'E0';
                break;
        }
        $errorMsg = array();
        $errorMsg['callState'] = 1;
        $errorMsg['errorMsg'] = $msg;
        $errorMsg['response'] = array();
        return $errorMsg;
        exit();
    }

    function responseCall($data)
    {
        $runTime = round($this->caclutime() - $this->st, 5);
        Log::info('bg/'.$this->apiName . '-' . date('Y-m-d H:i:s') . '-runTime: ' . $runTime);
        $responseData = array();
        $responseData['callState'] = 0;
        $responseData['errorMsg'] = "";
        $responseData['response'] = $data;
        return $responseData;
    }

}

function isApiExist($apiName)
{
    if (isset($apiName)) {
        if (file_exists(dirname(__FILE__) . '//bg//' . $apiName . '.php')) {
            include_once dirname(__FILE__) . '//bg//' . $apiName . '.php';
            if (class_exists('Bg\\Api\\' . $apiName)) {
                return true;
            }
        }
    }
    return false;
}
