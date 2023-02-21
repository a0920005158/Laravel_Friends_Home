<?php
namespace App\Http\Controllers;

header('Access-Control-Allow-Origin: *'); // 讓vue rwd可以跨網域呼叫

include_once dirname(dirname(dirname(__FILE__))) . '/_Sys/config_redis.php';
include_once dirname(dirname(dirname(__FILE__))) . '/_Pub/enDecryption.php';

use App\Models\member;
use Illuminate\Http\Request;

class lineCallbackController
{
    public function Run(Request $request)
    {
        $userId = $request->get('token');
        $code = $request->get('code');
        $data = array();
        $data["grant_type"] = "authorization_code";
        $data["redirect_uri"] = "https://ccf.bllin.net/lineCallback?token=" . $userId;
        $data["client_id"] = "TK0eE6xFNdwlpD7eF07RHS";
        $data["client_secret"] = "NTf0QLM6KwiViaMRPLTUb11Ged7zoBRXcE8i6HBj6DZ";
        $data["code"] = $code;
        $response = PostData_LineOauth($data);
        if ($response["status"] == "200") {
            $message = $response["message"];
            $access_token = $response["access_token"];

            PostData_LineNotify($access_token, "Line通知綁定 【成功】");
            if ($this->storeNotifyToken($userId, $access_token)) {
                return view('lineNotifyResult', [
                    'result' => "Line通知綁定 【成功】" . $access_token,
                ]);
            } else {
                return view('lineNotifyResult', [
                    'result' => "Line通知綁定 【失敗】" . $access_token,
                ]);
            }
        } else {
            return view('lineNotifyResult', [
                'result' => "Line通知綁定 【失敗】" . $response["status"],
            ]);
        }
    }
    public function storeNotifyToken($userId, $token)
    {
        $member_model = new member();
        $result = $member_model->where('line_user_id', $userId)->update(['line' => $token]);
        return $result;
    }
}

function PostData_LineOauth($array_jumppost)
{
    $serverURL = 'https://notify-bot.line.me/oauth/token';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $serverURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $array_jumppost);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $HTTPResponse = curl_exec($ch);
    $resultArray = curl_getinfo($ch);
    $http_header_code = $resultArray["http_code"];
    curl_close($ch);
    return json_decode($HTTPResponse, true);
}

function PostData_LineNotify($Token, $message)
{
    $headers = array(
        'Content-Type: multipart/form-data',
        'Authorization: Bearer ' . $Token,
    );
    $message = array(
        'message' => $message,
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    curl_close($ch);
}
