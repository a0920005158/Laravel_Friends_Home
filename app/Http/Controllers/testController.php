<?php

namespace App\Http\Controllers;

header('Access-Control-Allow-Origin: *'); // 讓vue rwd可以跨網域呼叫

include_once dirname(dirname(dirname(__FILE__))) . '/_Sys/config_redis.php';

use App\Models\member;
use Illuminate\Http\Request;
use Log;

class testController
{
    public function Run(Request $request)
    {
        $st = $this->caclutime();
        $stDate = date('Y-m-d H:i:s:u');
        $requestTime = $request->get('requestTime');
        $testConnection = $request->get('testConnection');
        $data = array();
        switch ($testConnection) {
            case 'db':
                $data = $this->connectDB();
                break;
            case 'redis':
                $data = $this->connectRedis();
                // sleep(20);
                break;
        }
        $et = $this->caclutime();
        $runTime = round($et - $st, 5);
        Log::info('Test-' . date('Y-m-d H:i:s') . '-runTime: ' . $runTime);
        return array('data' => $data, 'runTime' => $runTime);
    }

    function caclutime()
    {
        $time = explode(" ", microtime());
        $usec = (float)$time[0];
        $sec = (float)$time[1];
        return $sec + $usec;
    }

    public function connectDB()
    {
        $member_model = new member();
        $result = $member_model::all();
        return $result;
    }

    public function connectRedis()
    {
        $redis = getRedis();
        $result = $redis->hget("role_power", 1);
        return $result;
    }
}
