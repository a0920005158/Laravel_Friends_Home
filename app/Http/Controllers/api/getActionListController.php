<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\service_action;

class getActionListController extends apiAjax
{
    public function Run()
    {
        $startdate = $this->getParam('startdate');
        $enddate = $this->getParam('enddate');
        $data = $this->getActionListData($startdate, $enddate);
        return $data;
    }

    public function getActionListData($startdate, $enddate)
    {
        $result = array();
        if ($startdate == null || $enddate == null) {
            return service_action::orderBy('startdate', 'desc')->get();
        } else {
            $s_a_model = new service_action();
            $result = $s_a_model->where('startdate', '>=', $startdate)->where('enddate', '<=', $enddate)->get();
        }
        usort($result, array($this, "cmp"));

        return $result;
    }

    public function cmp($a, $b)
    {
        return $a['startdate'] < $b['startdate'];
    }
}
