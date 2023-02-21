<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\member;
use App\Models\service_action;

class sendLineActionMsgController extends apiAjax
{
    public function Run()
    {
        $aid = $this->getParam('aid');
        $Action = $this->getActionListData($aid);
        $errorMid = array();
        $successMid = array();
        foreach ($Action as $key => $value) {
            foreach (explode(",", $value['actionmem']) as $midkey => $mid) {
                $msg = "\n家友活動提醒‼️: \n";
                $msg .= "活動名稱: " . $value['name'] . "\n";
                $msg .= "活動時間: 📅\n" . $value['startdate'] . ' ~ ' . $value['enddate'] . "\n";

                $lineToken = $this->getUserLineToken($mid);
                if ($lineToken != null) {
                    $result = $this->PostData_LineNotify($lineToken, $msg);
                    if ($result["status"] == 200 && $result["message"] == "ok") {
                        array_push($successMid, $mid);
                    } else {
                        array_push($errorMid, $mid);
                    }
                } else {
                    array_push($errorMid, $mid);
                }
            }
        }
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

    public function getActionListData($aid)
    {
        $service_action_model = new service_action();
        $service = $service_action_model->where('aid', $aid)->get();
        $result = array();
        foreach ($service as $key => $value) {
            $result[$key]['name'] = $value['name'];
            $result[$key]['startdate'] = $value['startdate'];
            $result[$key]['enddate'] = $value['enddate'];
            $result[$key]['actionmem'] = $value['actionmem'];
            $result[$key]['principal'] = $value['principal'];
            $result[$key]['aid'] = $value['aid'];
        }

        return $result;
    }
}
