<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\member;
use App\Models\service_action;

class addActionPrincipalController extends apiAjax
{
    public function Run()
    {
        $aid = $this->getParam('aid');
        $principal = $this->getParam('principal');
        if (is_numeric($principal)) {
            if ($this->addPrincipal($aid, $principal)) {
                return array();
            } else {
                $this->runError('會員新增失敗!');
            }
        } else {
            $this->runError('格式錯誤!');
        }
    }

    public function addPrincipal($aid, $principal)
    {
        $member_model = new member();
        $exist_member_count = count($member_model->where('mid', $principal)->get());
        if ($exist_member_count == 1) {
            $service_action_model = new service_action();
            $action = $service_action_model->where('aid', $aid)->update(['principal' => $principal]);
            return $action;
        } else {
            return false;
        }
    }
}
