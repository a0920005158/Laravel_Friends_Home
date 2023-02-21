<?php
//TODO 有沒有最新的 redis 寫法
$sys_redis = null;
$sys_redis_dbIndex = 3;

function getRedis()
{
    // global $sys_redis, $sys_redis_dbIndex;
    $sys_redis = null;
    $sys_redis_dbIndex = 3;

    if ($sys_redis != null) {
        $sys_redis->select($sys_redis_dbIndex);
        return $sys_redis;

    }
    if ($sys_redis != null) {
        return $sys_redis;
    }

    require_once dirname(dirname(__FILE__)) . '/_lib/Predisx/Autoloader.php';
    Predisx\Autoloader::register();
    $sys_redis = new Predisx\Client(array(
        'scheme' => 'tcp',
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => 'w1x9g5k74p1t6i6a',
    ));
    $sys_redis->select($sys_redis_dbIndex);
    return $sys_redis;
}
