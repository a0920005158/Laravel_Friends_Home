<?php

namespace Bg\Api;

use App\BottomLayerClass\apiAjax;
use App\Models\member;
use App\Models\service_action;
ini_set('display_errors','1');
error_reporting(E_ALL);
class checkActionController extends apiAjax
{
    public function Run()
    {
        $todayAction = $this->getActionListData();
        $errorMid = array();
        $successMid = array();
        
        foreach ($todayAction as $key => $value) {
            foreach (explode(",", $value['actionmem']) as $midkey => $mid) {
                $msg = "\n家友活動提醒‼️: \n";
                $msg .= "活動名稱: " . $value['name'] . "\n";
                $msg .= "活動時間: 📅\n" . $value['startdate'] . ' ~ ' . $value['enddate'] . "\n";

                $lineToken = $this->getUserLineToken($mid);
                if ($lineToken != null) {
                    $result = $this->PostData_LineNotify($lineToken, $msg);
                    if($value['principal']==$mid)
                    if ($result["status"] == 200 && $result["message"] == "ok") {
                        if($value['principal']==$mid){ 
                            $input_text = base64_encode(date("Y-m-d"));
                            $url = "https://chart.googleapis.com/chart?chs=" . "100x100" . "&cht=qr&chl=" . $input_text . "&choe=UTF-8&chld=M";
                            // $this->PostData_LineNotify($lineToken, $url);
                            $result2 = $this->PostData_LineNotify_Img($lineToken, $url,"下圖為家友會活動簽到使用QR code，請會員掃瞄簽到");
                        }
                        array_push($successMid, $mid);
                    } else {
                        array_push($errorMid, $mid);
                    }
                    $redis = getRedis();
                    $actionKey = $redis->hget("actionMember:" . $value['aid'], $mid);
                    if ($actionKey != null) {
                        $redis->hset("actionMember:" . $value['aid'], $mid, 'no');
                    }

                    if ($value['principal'] == $mid) {
                        $redis->hset("actionMember:" . $value['aid'], 'principal', $lineToken);
                    }
                } else {
                    array_push($errorMid, $mid);
                }
            }
        }
        $this->write("successMid: " . implode(",", $successMid) . " errorMid: " . implode(",", $errorMid), "checkAction");
        return array("errorMid" => $errorMid, "successMid" => $successMid);
    } 

    public function getUserLineToken($mid)
    {
        $lineToken = member::firstWhere('mid', $mid);
        if ($lineToken != null) {
            return $lineToken['line'];
        }
        return null;
    }

    public function getActionListData()
    {
        $todayS = date("Y-m-d", strtotime(date("Y-m-d") . "+1 day")) . ' 00:00:00';
        $todayE = date("Y-m-d", strtotime(date("Y-m-d") . "+1 day")) . ' 23:59:59';
        $service_action_model = new service_action();
        $service = $service_action_model->where('startdate', '>=', $todayS)->where('startdate', '<=', $todayE)->get();
        $result = array();
        if ($service !== null) {
            foreach ($service as $key => $value) {
                $result[$key]['name'] = $value['name'];
                $result[$key]['startdate'] = $value['startdate'];
                $result[$key]['enddate'] = $value['enddate'];
                $result[$key]['actionmem'] = $value['actionmem'];
                $result[$key]['principal'] = $value['principal'];
                $result[$key]['aid'] = $value['aid'];
            }
        }

        return $result;
    }
}
