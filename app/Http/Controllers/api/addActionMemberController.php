<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\member;
use App\Models\service_action;

class addActionMemberController extends apiAjax
{
    public function Run()
    {
        $aid = $this->getParam('aid');
        $addmid = $this->getParam('addmid');
        if ($this->formatCheck($addmid)) {
            if ($this->addMember($aid, $addmid)) {
                return array();
            } else {
                $this->runError('會員新增失敗!');
            }
        } else {
            $this->runError('格式錯誤!');
        }
    }

    public function formatCheck($addmid)
    {
        if ($addmid == '' || $addmid == null) {
            return true;
        } else {
            $pattern = "/^\d{1,}(,\d{1,}){0,}$/";
            $result = preg_match($pattern, $addmid);
            return $result;
        }
    }

    public function addMember($aid, $addmid)
    {
        $member_model = new member();
        $add_member_count = explode(',', $addmid);
        $exist_member_count = count($member_model->whereIn('mid', $add_member_count)->get());
        if (count($add_member_count) == $exist_member_count) {
            $service_action_model = new service_action();
            $action = $service_action_model->where('aid', $aid)->update(['actionmem' => $addmid]);
            return $action;
        } else {
            return false;
        }
    }
}
