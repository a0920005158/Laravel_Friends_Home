<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\member;
use App\Models\service_action;

class sendActionInviteController extends apiAjax
{
    public function Run()
    {
        $aid = $this->getParam('aid');
        $Action = $this->getActionListData($aid);
        if($Action!=null){
            $msg = "::::" . $Action['name'] . "活動○號召夥伴共襄盛::::\n";
            $msg .= "活動時間: 📅\n" . $Action['startdate'] . ' ~ ' . $Action['enddate'] . "\n";
            $msg .= "窗口：" . $this->getActionMems($Action['principal']) . "\n";
            $msg .= "目前參加名單:" . $this->getActionMems($Action['actionmem']) . "\n";
            // $msg .= "若夥伴有興趣參加請輸入 /adac%" . $Action['aid'] . " 加入活動 感謝您~\n";
            $msg .= "若夥伴有興趣參加請點擊以下網址進行報名:\n";
            $msg .= "https://liff.line.me/1655869245-O17LemkA\n";
            $msg .= "加入活動 感謝您~\n";
            $result = $this->PostData_LineNotify("OCfUcFzIGs6qjYUkSMGcPEDgoExhYtkbJ56XFwh6aK2", $msg);
    
            if ($result["status"] == 200 && $result["message"] == "ok") {
                return array();
            } else {
                $this->runError('發生錯誤!');
            }
        }else{
            $this->runError('發生錯誤!');
        }
    }

    public function getActionMems($actionmems)
    {
        $memsArray = explode(",", $actionmems);
        $member_model = new member();
        $userdata = $member_model->whereIn('mid', $memsArray)->get();
        $result = '';
        if (count($userdata) > 0) {
            foreach ($userdata as $key => $value) {
                if ($key == 0) {
                    $result .= $value['name'];
                } else {
                    $result .= ',' . $value['name'];
                }
            }
        }
        return $result;
    }

    public function getActionListData($aid)
    {
        $data = service_action::firstWhere('aid', $aid);
        $result = array();
        if ($data != null) {
            $result['name'] = $data['name'];
            $result['startdate'] = $data['startdate'];
            $result['enddate'] = $data['enddate'];
            $result['actionmem'] = $data['actionmem'];
            $result['principal'] = $data['principal'];
            $result['aid'] = $data['aid'];
            return $result;
        } else {
            return null;
        }
    }
}
