<?php

namespace Bg\Api;

use App\BottomLayerClass\apiAjax;
use App\Models\member;
use App\Models\service_action;

class cancelActionMemberController extends apiAjax
{
    public function Run()
    {
        $aid = $this->getParam('aid');
        $userId = $this->getParam('userId');
        $mid = $this->getMid($userId);
        if ($mid != null) {
            if (!$this->isExpired($aid)) {
                if ($this->cancelMember($aid, $mid)) {
                    $sendMsg = $this->curl_post('https://ccf.bllin.net/bgx/sendActionInvite', "aid=" . $aid);
                    if ($sendMsg->{"callState"} == 1) {
                        
                    }
                    return array();
                } else {
                    $this->runError('取消參加活動失敗!');
                }
            } else {
                $this->runError('活動已結束!');
            }
        } else {
            $this->runError('請創建帳號，並通知管理員開通!');
        }
    }

    public function isExpired($aid)
    {
        $service_action_model = new service_action();
        $nowTime = date('Y-m-d H:i:s');
        $result = $service_action_model->where('aid', $aid)->where('enddate', '<', $nowTime)->get();
        if (count($result) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getMid($userId)
    {
        $result = member::firstWhere('line_user_id', $userId);
        if ($result != null) {
            return $result['mid'];
        }
        return null;
    }

    public function cancelMember($aid, $mid)
    {
        $service_action_model = new service_action();
        $actionMems = $this->getActionMembers($aid);
        $actionMemsArrays = explode(',', $actionMems);
        $actionMemsIndex = array_search($mid, $actionMemsArrays);
        if ($actionMemsIndex!==false) {
            $this->array_remove($actionMemsArrays, $actionMemsIndex);
            $updateMems = implode(",",$actionMemsArrays);
            $result = $service_action_model->where('aid', $aid)->update(['actionmem' => $updateMems]);
            return $result;
        }else{
            return false;
        }
    }

    public function getActionMembers($aid)
    {
        $userdata = service_action::firstWhere('aid', $aid);
        return $userdata != null ? $userdata['actionmem'] : $userdata;
    }

    public function array_remove(&$arr, $offset)
    {
        array_splice($arr, $offset, 1);
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
