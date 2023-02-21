<?php

namespace App\Http\Controllers;

header('Access-Control-Allow-Origin: *'); // 讓vue rwd可以跨網域呼叫

include_once dirname(dirname(dirname(__FILE__))) . "/BottomLayerClass/LineLogin.class.php";
include_once dirname(dirname(dirname(__FILE__))) . '/_Sys/config_redis.php';
include_once dirname(dirname(dirname(__FILE__))) . '/_Pub/enDecryption.php';

use Illuminate\Http\Request;
use App\Models\member;
use LineLogin;

class Line_LoginController
{
    public function Run(Request $request)
    {
        // echo '維護中!';
        // exit();

        $state = $request->get("state");
        $code = $request->get("code");
        $app_access_token = $request->get("app_access_token");
        $client_id = "1655869245";
        $client_secret = "c6598a22211e5a74dac78545f1848714";
        $redirect_uri = "https://ccf.bllin.net/Line_Loginx";
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
        } else {
            $lineLogin->reAuthPermission();
            exit();
        }

        $userData = $this->userIdAutGetUserData($userId);
        if ($userData != null) {
            $mid = $userData['mid'];
            $name = $userData['name'];
        } else {
            if ($name == null || $name == "" || $userId == null || $userId == "") {
                echo '請開啟Line授權!';
                exit();
            } else {
                $mid = $this->createAcc($name, $userId);
                if (!$mid) {
                    $lineLogin->errorRedir();
                    exit();
                } else {
                    echo '帳號創建成功，請重新登入!';
                    exit();
                }
            }
        }

        $random = encryption($mid . $name . time());
        $this->redisSetUserData($random, $userData);
        setcookie("random", $random);
        $lineLogin->urlRedir('https://ccf.bllin.net/UI/admin.html#/lobby/');
        exit();
    }

    public function createAcc($name, $userId)
    {
        if (isset($name) && isset($userId)) {
            $member_model = new member();
            $haveMem = $member_model->where('name', $name)->where('line_user_id', $userId)->get();
            if (count($haveMem) == 0) {
                try {
                    $member_model->create([
                        'name' => $name,
                        'line_user_id' => $userId,
                        'pass' => 1
                    ]);
                    return true;
                } catch (\Illuminate\Database\QueryException $exception) {
                    return false;
                }
            }
        }
        return false;
    }

    public function userIdAutGetUserData($userId)
    {
        $member_model = new member();
        $userdata = $member_model->where('line_user_id', $userId)->get();
        if (count($userdata) > 0 && $userdata[0]['pass'] == 1) {
            return $userdata[0];
        } else {
            return null;
        }
    }

    public function redisSetUserData($random, $userData)
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
}
