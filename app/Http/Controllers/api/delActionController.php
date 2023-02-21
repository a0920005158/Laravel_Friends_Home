<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\service_action;

class delActionController extends apiAjax
{
    public function Run()
    {
        $aid = $this->getParam('aid');

        if (is_numeric($aid)) {
            if ($this->delAction($aid)) {
                return array();
            } else {
                $this->runError('刪除失敗!');
            }
        } else {
            $this->runError('輸入格式錯誤!');
        }
    }

    public function delAction($aid)
    {
        $service_action_model = new service_action();
        $result = $service_action_model->where('aid', $aid)->delete();
        return $result;
    }
}
