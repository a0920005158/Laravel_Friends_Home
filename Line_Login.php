<?php
include "LineLogin.class.php";
include_once './_sys/config_redis.php';
include_once './_pub/dbControl.php';
include_once './_pub/enDecryption.php';

// echo '維護中!';
// exit();

session_start();
$access_token = $_SESSION['access_token'];
$refresh_token = $_SESSION['refresh_token'];
$state = $_GET["state"];
$code = $_GET["code"];
$app_access_token = $_GET["app_access_token"];
$client_id = "1655869245";
$client_secret = "c6598a22211e5a74dac78545f1848714";
$redirect_uri = "https://joan.bllin.net/Line_Loginx";
$userId = '';
$name = '';
$redis = getRedis();
$lineLogin = new LineLogin($client_id, $client_secret, $redirect_uri);
if (isset($app_access_token)) {
    $profile = $lineLogin->accTokenGetProfile($app_access_token);
    if (isset($profile['userId']) && isset($profile['displayName'])) {
        $userId = $profile['userId'];
        $name = $profile['displayName'];
    } else {
        echo '發生錯誤!';
        exit();
    }
} else if (isset($code) && isset($state)) {
    if ($redis->get("lineState:" . $state) != null) {
        $redis->del("lineState:" . $state);
        //Access token -------------------------------------
        $re = $lineLogin->getTokenByCode($code);
        if (isset($re["id_token"]) && isset($re["refresh_token"]) && isset($re["access_token"])) {
            $id_token = $re["id_token"];
            $refresh_token = $re["refresh_token"];
            $access_token = $re["access_token"];
            $profile = $lineLogin->decodeID_Token($id_token);
        } else {
            $lineLogin->errorRedir();
            exit();
        }
    } else {
        $lineLogin->errorRedir();
        exit();
    }
    $userId = $profile->{'sub'};
    $name = $profile->{'name'};
} else if (isset($refresh_token) && isset($access_token)) {
    if ($lineLogin->verifyAccToken($access_token)) {
        $profile = $lineLogin->accTokenGetProfile($access_token);
    } else {
        $newAccess_token = $lineLogin->getTokenByRefreshToken($refresh_token);
        if (isset($newAccess_token["access_token"]) && isset($newAccess_token["refresh_token"])) {
            $access_token = $newAccess_token["access_token"];
            $refresh_token = $newAccess_token["refresh_token"];
            $profile = $lineLogin->accTokenGetProfile($newAccess_token["access_token"]);
        } else {
            $lineLogin->reAuthPermission();
            exit();
        }
    }
    $userId = $profile['userId'];
    $name = $profile['displayName'];
} else {
    $lineLogin->reAuthPermission();
    exit();
}

$userData = userIdAutGetUserData($userId);

if ($userData != null) {
    $mid = $userData['mid'];
    $name = $userData['name'];
} else {
    if ($name == null || $name == "" || $userId == null || $userId == "") {
        echo '請開啟Line授權!';
        exit();
    } else {
        $mid = createAcc($name, $userId);
        if (!$mid) {
            $lineLogin->errorRedir();
            exit();
        }
    }
}

$random = encryption($mid . $name . time());
redisSetUserData($random, $userData);
$_SESSION['access_token'] = $access_token;
$_SESSION['refresh_token'] = $refresh_token;
$lineLogin->urlRedir('https://joan.bllin.net/UI/admin.html?token=' . $random);
exit();

function createAcc($name, $userId)
{
    if (isset($name) && isset($userId)) {
        $sql = "SELECT mid FROM member WHERE name = ? AND line_user_id = ?";
        $haveMem = db_queryOne($sql, array($name, $userId));
        if (!$haveMem) {
            $sqlArray = array($name, $userId);
            $result = db_pUpdate("INSERT INTO member(name,line_user_id) VALUES(?,?)", $sqlArray);
            return $result;
        }
        return false;
    } else {
        return false;
    }
}

function userIdAutGetUserData($userId)
{
    $sql = "SELECT mid,acc,job,joinDate,type,line,phone,birthDay,name FROM member WHERE line_user_id = ? AND pass = 1";
    $userdata = db_queryOne($sql, array($userId));
    if ($userdata) {
        $data = array();
        $data['mid'] = $userdata[0];
        $data['acc'] = $userdata[1];
        $data['job'] = $userdata[2];
        $data['joinDate'] = $userdata[3];
        $data['type'] = $userdata[4];
        $data['line'] = $userdata[5];
        $data['phone'] = $userdata[6];
        $data['birthDay'] = $userdata[7];
        $data['name'] = $userdata[8];
        $data['userId'] = $userId;
        return $data;
    } else {
        return null;
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

    $previousR = $redis->hget("userCall:" . $userData['userId'], "r");
    if ($previousR != null) {
        $redis->del("userCall:" . $userData['userId']);
        $redis->del("userData:" . $previousR);
    }

    $redis->hset("userCall:" . $userData['userId'], "r", $random);
    $redis->hset("userCall:" . $userData['userId'], "dateTime", date("Y-m-d H:i:s"));
}
