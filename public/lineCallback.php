<?php

echo '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>';
if (!array_key_exists("code", $_POST)) {
    echo "Line通知綁定 【失敗】 <a id='timer'>5</a>秒後轉跳";
} else {
    $code = $_POST['code'];
    $id_token = $_POST['id_token'];
    $phone = $params['state'];
    //iLog("LINE code:" . $code . " phone:" . $phone);
    $data = array();
    $data["grant_type"] = "authorization_code";
    $data["redirect_uri"] = "https://joan.bllin.net/lineCallback.php";
    $data["client_id"] = "TK0eE6xFNdwlpD7eF07RHS";
    $data["client_secret"] = "NTf0QLM6KwiViaMRPLTUb11Ged7zoBRXcE8i6HBj6DZ";
    $data["code"] = $code;
    $response = PostData_LineOauth($data);
    //iLog("LINE response:" . serialize($response));
    if ($response["status"] == "200") {
        $message = $response["message"];
        $access_token = $response["access_token"];

        PostData_LineNotify($access_token, "Line通知綁定 【成功】");

        echo "Line通知綁定 【成功】 <a id='timer'>5</a>秒後轉跳  " . $access_token;
        echo '<br>---------<br>';
        print_r($response);
    } else {
        echo "Line通知綁定 【失敗】 <a id='timer'>5</a>秒後轉跳";
    }
}

echo "<script type='text/javascript'>
    setTimeout('countdown()', 1000);
    function countdown() {
        var s = document.getElementById('timer');
        s.innerHTML = s.innerHTML - 1;
        if (s.innerHTML == 0)
        window.location = 'http://34.80.73.169/';
        else
        setTimeout('countdown()', 1000);
    } </script>";

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

    //iLog("LINE PostData_LineNotify:" . $Token . " message:" . $message);
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

    //iLog("LINE PostData_LineNotify result: " . $result);
    curl_close($ch);
}

exit();
