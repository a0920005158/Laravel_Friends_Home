<?php
include_once dirname(dirname(__FILE__)) . '/app/_Sys/config_redis.php';

$st = caclutime();
$data = connectRedis();
$et = caclutime();
$runTime = round($et - $st, 5);
		
echo json_encode(array('data' => $data, 'runTime' => $runTime ));

function connectRedis()
{
   $redis = getRedis();
   $result = $redis->hget("role_power", 1);
   return $result;
}

function caclutime()
    {
        $time = explode(" ", microtime());
        $usec = (float)$time[0];
        $sec = (float)$time[1];
        return $sec + $usec;
    }
?>