<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\members_sign_table;

class getTotalHoursController extends apiAjax
{
    public function Run()
    {
        $mid = $this->getParam('mid');
        $st = $this->getParam('st');
        $et = $this->getParam('et');
        $type = $this->getParam('type');
        $data = $this->getTotalHoursData($mid, $st, $et, $type);
        return array('st' => $st, 'et' => $et, 'hourData' => $data);
    }

    public function getTotalHoursData($mid, $st, $et, $type)
    {
        $sql = new members_sign_table();
        if ($mid != 'all') {
            $sql = $sql->where('mid', $mid);
        }
        $sql = $sql->where('start_sign_time', '>=', $st)->where('end_sign_time', '<=', $et);
        if ($type != '-1') {
            $sql = $sql->where('type', $type);
        }
        $sql = $sql->groupBy('mid');
        $sql = $sql->selectRaw('mid,name,sum(TIMESTAMPDIFF(SECOND, start_sign_time, end_sign_time)) as total_time');
        $sqlResult = $sql->orderBy('total_time', 'desc')->groupBy('mid')->get();

        $result = array();
        if ($sqlResult !== null) {
            foreach ($sqlResult as $key => $value) {
                $result[$key]['mid'] = $value['mid'];
                $result[$key]['name'] = $value['name'];
                $result[$key]['total_time'] = number_format($value['total_time'] / 60 / 60, 2);
            }
        }

        return $result;
    }
}
