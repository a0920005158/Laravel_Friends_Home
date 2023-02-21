<?php

namespace Bg\Api;

use App\BottomLayerClass\apiAjax;
use App\Models\service_action;

class checkAutoInviteController extends apiAjax
{
    public function Run()
    {
        $todayAction = $this->getActionListData();
        $errorAid = array();
        $successAid = array();
        foreach ($todayAction as $key => $value) {
            $sendMsg = $this->curl_post('https://ccf.bllin.net/bgx/sendActionInvite', "aid=" . $value['aid']);
            if ($sendMsg->{"callState"} == 1) {
                array_push($errorAid, $value['aid']);
            } else {
                array_push($successAid, $value['aid']);
            }
        }
        $this->write("successAid: " . implode(",", $successAid) . " errorAid: " . implode(",", $errorAid), "checkAutoInvite");
        return array("errorAid" => $errorAid, "successAid" => $successAid);
    }

    public function getActionListData()
    {
        $today = date("Y-m-d H:i:s");
        $service_action_model = new service_action();
        $service = $service_action_model->where('startdate', '>', $today)->where('auto_invite', 1)->get();
        $result = array();
        if ($service !== null) {
            foreach ($service as $key => $value) {
                $nowMemCount = $value['actionmem'] == null || $value['actionmem'] == "" ? 0 : count(explode(",", $value['actionmem']));
                if ($nowMemCount < $value['number_people']) {
                    $result[$key]['name'] = $value['name'];
                    $result[$key]['startdate'] = $value['startdate'];
                    $result[$key]['enddate'] = $value['enddate'];
                    $result[$key]['actionmem'] = $value['actionmem'];
                    $result[$key]['principal'] = $value['principal'];
                    $result[$key]['aid'] = $value['aid'];
                    $result[$key]['number_people'] = $value['number_people'];
                }
            }
        }

        return $result;
    }
}
