<?php

namespace Bg\Api;

use App\BottomLayerClass\apiAjax;
use App\Models\sign_in_record;

class getTotalHoursController extends apiAjax
{
    public function Run()
    {
        $userId = $this->getParam('userId');
        $st = $this->getParam('st');
        $et = $this->getParam('et');
        if ($st == null || $st == "" || $et == null || $et == "") {
            $st = date("Y") . "-01-01 00:00:00";
            $et = date("Y-m-d H:i:s");
        }

        $data = $this->getTotalHoursData($userId, $st, $et);
        return array('st' => $st, 'et' => $et, 'hourData' => $data);
    }

    public function getTotalHoursData($userId, $st, $et)
    {
        $sign_in_record_model = new sign_in_record();
        $signInTable = $sign_in_record_model->join('member', 'sign_in_record.mid', '=', 'member.mid');
        $signInTable = $signInTable->where('line_user_id', $userId)->where('start_sign_time', '>=', $st)->where('end_sign_time', '<=', $et);
        $signInTable = $signInTable->select('member.mid', 'member.name', 'sign_in_record.start_sign_time', 'sign_in_record.end_sign_time')->get();
        $totalData = array('mid' => '', 'name' => '', 'total_time' => 0);
        foreach ($signInTable as $key => $value) {
            $totalData['mid'] = $value['mid'];
            $totalData['name'] = $value['name'];
            $totalData['total_time'] = number_format($totalData['total_time'] + ((strtotime($value['end_sign_time']) - strtotime($value['start_sign_time'])) / 60 / 60),2);
        }

        return $totalData;
    }
}
