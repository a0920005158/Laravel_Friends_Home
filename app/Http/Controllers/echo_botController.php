<?php

namespace App\Http\Controllers;

header('Access-Control-Allow-Origin: *'); // 讓vue rwd可以跨網域呼叫

include_once dirname(dirname(dirname(__FILE__))) . '/_Sys/config_redis.php';
include_once dirname(dirname(dirname(__FILE__))) . '/_Pub/enDecryption.php';
include_once dirname(dirname(dirname(__FILE__))) . '/_Pub/lineEchoBot.class.php';
include_once dirname(dirname(dirname(__FILE__))) . '/_Pub/postApi.php';
include_once dirname(dirname(dirname(__FILE__))) . '/_Pub/LINEBotTiny.php';

use App\_Pub\lineEchoBot;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Log;

class echo_botController extends BaseController
{
    public function Run(Request $request)
    {
        $st = $this->caclutime();
        $ip = $request->ip();
        $channelAccessToken = 'wuiiyhKFDa3mg0ZjiqfuychtvyO3QGFddGUxafxT8k1t+8KHWi+geKDW0em8YwksBNHuA+P4TTGtuoSWi/fKqtTZ64vdQRNHAf5Hx75h8FIa9QSJbDB3I6OLHNOnAvqnoCT1fvkfeC5lu3VMPyMRBgdB04t89/1O/w1cDnyilFU=';
        $channelSecret = '5774f7a3997f3c6e5b4c6be941ffc289';
        $foregin1St = $this->caclutime();
        $echoBot = new lineEchoBot($channelAccessToken, $channelSecret);
        $foregin1Et = $this->caclutime();
        $foregin2St = 0;
        $foregin2Et = 0;
        $hookMsg = $echoBot->getMsg();
        foreach ($hookMsg as $key => $value) {
            $cmd = explode("%", $value["text"]);
            $replyMsg = '';
            switch ($cmd[0]) {
                case '/count':
                    $foregin2St = $this->caclutime();
                    $TotalHoursData = $this->curl_post('https://ccf.bllin.net/bgx/getTotalHours', "userId=" . $value["userId"] . "&st=&et=");
                    $foregin2Et = $this->caclutime();
                    $replyMsg = '📅日期:' . substr($TotalHoursData->{"response"}->{"st"}, 0, -9) . '~' . substr($TotalHoursData->{"response"}->{'et'}, 0, -9) . "\n" . ' 🎉🎉今年總時數為: ' . $TotalHoursData->{"response"}->{'hourData'}->{'total_time'} . ' hr 🎉🎉';
                    break;
                case '/beacon':
                    $foregin2St = $this->caclutime();
                    $sign = curl_post('https://ccf.bllin.net/bgx/actionSignIn', "line_user_id=" . $value["userId"]);
                    $foregin2Et = $this->caclutime();
                    if ($sign->{"callState"} == 1) {
                        $replyMsg = '您於 ' . date('Y-m-d H:i') . ' 簽到失敗，請通知管理員處理!';
                    } else if ($sign->{"callState"} == 0 && $sign->{"response"}->{"signState"} == 1) {
                        $replyMsg = '您於 ' . date('Y-m-d H:i') . ' 完成簽到!';
                    } else if ($sign->{"callState"} == 0 && $sign->{"response"}->{"signState"} == 2) {
                        $replyMsg = '您於 ' . date('Y-m-d H:i') . ' 完成簽退!';
                    }
                    break;
                    // case '/adac':
                    //     $sign = curl_post('https://ccf.bllin.net/bgx/addActionMember', "addUserId=" . $value["userId"] . "&aid=" . $cmd[1]);
                    //     if ($sign->{"callState"} == 1) {
                    //         $replyMsg = $sign->{"errorMsg"};
                    //     } else {
                    //         $sendMsg = curl_post('https://ccf.bllin.net/bgx/sendActionInvite', "aid=" . $cmd[1]);
                    //         if ($sendMsg->{"callState"} == 1) {
                    //             $replyMsg = $sendMsg->{"errorMsg"};
                    //         }
                    //     }
                    //     break;
            }

            if ($replyMsg != '') {
                $echoBot->replyMessage($replyMsg, $value["replyToken"]);
            }

            $et = $this->caclutime();
            $runTime = round($et - $st, 5);
            $conLineTime = round($foregin1Et - $foregin1St, 5);
            $apiCallTime = round($foregin2Et - $foregin2St, 5);
            Log::info('echo_botController-' . date('Y-m-d H:i:s') . '-runTime: ' . $runTime.' ip:'.$ip.' replyMsg:'.$replyMsg.' replyToken:'.$value["replyToken"].' conLineTime:'.$conLineTime.' apiCallTime:'.$apiCallTime);
        }
    }
    
    public function caclutime()
    {
        $time = explode(" ", microtime());
        $usec = (float)$time[0];
        $sec = (float)$time[1];
        return $sec + $usec;
    }

    public function curl_post($url, $data = "")
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        // var_dump($resp);

        return json_decode($resp);
    }
}
