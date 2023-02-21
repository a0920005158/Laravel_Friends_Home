<?php

namespace App\Http\Controllers\Api;

use App\BottomLayerClass\apiAjax;
use App\Models\members_sign_table;

class getSignDetailController extends apiAjax
{
    public function Run()
    {
        $mid = $this->getParam('mid');
        $st = $this->getParam('st');
        $et = $this->getParam('et');
        $type = $this->getParam('type');
        $currentPage = $this->getParam('currentPage');
        $data = $this->getSignDetailData($mid, $st, $et, $type, $currentPage, 20);
        return array('st' => $st, 'et' => $et, 'signDetailData' => $data);
    }

    public function getSignDetailData($mid, $st, $et, $type, $currentPage, $count)
    {
        $members_sign_table_model = new members_sign_table();
        $sql = $members_sign_table_model->where('mid', $mid)->where('start_sign_time', '>=', $st)->where('end_sign_time', '<=', $et);
        if ($type != '-1') {
            $sql = $sql->where('type', $type);
        }
        $totalCount = $sql->count();
        $sqlResult = array('totalPage' => ceil((int) $totalCount / $count), 'currentPage' => $currentPage);
        $result = array();
        $pageData = $sql->offset(((int) $currentPage - 1) * $count)->limit($count)->get();
        foreach ($pageData as $key => $value) {
            $result[$key]['name'] = $value['name'];
            $result[$key]['start_sign_time'] = $value['start_sign_time'];
            $result[$key]['end_sign_time'] = $value['end_sign_time'];
            $result[$key]['action_name'] = $value['action_name'];
            $result[$key]['total_time'] = number_format((strtotime($value['end_sign_time']) - strtotime($value['start_sign_time'])) / 60 / 60, 2) . ' hr';
        }
        $sqlResult['data'] = $result;
        return $sqlResult;
    }
}
