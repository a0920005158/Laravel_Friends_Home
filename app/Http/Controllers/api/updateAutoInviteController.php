<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\service_action;

class updateAutoInviteController extends apiAjax
{
    public function Run()
    {
        $aid = $this->getParam('aid');
        $inviteAuto = $this->getParam('inviteAuto');
        if (is_numeric($aid) && ($inviteAuto == true || $inviteAuto == false)) {
            if ($this->updateInviteAutoX($aid, $inviteAuto)) {
                return array();
            } else {
                $this->runError('發生錯誤，請通知管理員處理!');
            }
        } else {
            $this->runError('格式錯誤!');
        }
    }

    public function updateInviteAutoX($aid, $inviteAuto)
    {
        $service_action_model = new service_action();
        if ($inviteAuto === "true") {
            $inviteAuto = 1;
        } else if ($inviteAuto === "false") {
            $inviteAuto = 0;
        }
        $result = $service_action_model->where('aid', $aid)->update(["auto_invite" => $inviteAuto]);
        return $result;
    }
}
