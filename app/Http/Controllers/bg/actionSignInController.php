<?php

namespace Bg\Api;

use App\BottomLayerClass\apiAjax;
use App\Models\member;
use App\Models\service_action;
use App\Models\sign_in_record;
use DB;

class actionSignInController extends apiAjax
{
    public function Run()
    {
        $userId = $this->getParam('line_user_id');
        $signTime = date('Y-m-d H:i:s');
        $mid = $this->getMid($userId);
        if ($mid != null) {
            $actionData = $this->joinAction($mid, $signTime);
            if (count($actionData) != 0) {
                $signResult = $this->signIn($actionData['aid'], $mid, $signTime);
                if ($signResult === 1) {
                    return array('signState' => 1);
                } else if ($signResult === 2) {
                    return array('signState' => 2);
                } else {
                    $this->runError('簽到新增失敗!');
                }
            } else {
                $this->runError('簽到新增失敗!');
            }
        } else {
            $this->runError('簽到新增失敗!');
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

    public function joinAction($mid, $signTime)
    {
        $service_action_model = new service_action();
        $sqlResult = $service_action_model->where(DB::raw('date_sub(startdate,interval 2 hour)'), '<=', $signTime)->where(DB::raw('date_add(enddate,interval 2 hour)'), '>=', $signTime)->get();
        $result = array();
        $difTime = 36000000;
        foreach ($sqlResult as $key => $value) {
            $difTimeTemp1 = strtotime($value['startdate']) - strtotime($signTime);
            $difTimeTemp2 = strtotime($signTime) - strtotime($value['enddate']);
            $isJoinAction = in_array($mid, explode(",", $value['actionmem']));
            if ($isJoinAction && strtotime($value['startdate']) <= strtotime($signTime) && strtotime($value['enddate']) >= strtotime($signTime)) {
                $result['name'] = $value['name'];
                $result['startdate'] = $value['startdate'];
                $result['enddate'] = $value['enddate'];
                $result['actionmem'] = $value['actionmem'];
                $result['principal'] = $value['principal'];
                $result['aid'] = $value['aid'];
                $difTime = 0;
            } else if ($isJoinAction && strtotime($value['startdate']) >= strtotime($signTime) && $difTimeTemp1 < $difTime) {
                $result['name'] = $value['name'];
                $result['startdate'] = $value['startdate'];
                $result['enddate'] = $value['enddate'];
                $result['actionmem'] = $value['actionmem'];
                $result['principal'] = $value['principal'];
                $result['aid'] = $value['aid'];
                $difTime = $difTimeTemp1;
            } else if ($isJoinAction && strtotime($value['enddate']) <= strtotime($signTime) && $difTimeTemp2 < $difTime) {
                $result['name'] = $value['name'];
                $result['startdate'] = $value['startdate'];
                $result['enddate'] = $value['enddate'];
                $result['actionmem'] = $value['actionmem'];
                $result['principal'] = $value['principal'];
                $result['aid'] = $value['aid'];
                $difTime = $difTimeTemp2;
            }
        }

        return $result;
    }

    public function getSignTable($aid, $mid)
    {
        $sign_in_record_model = new sign_in_record();
        $querydata = $sign_in_record_model->where('aid', $aid)->where('mid', $mid)->get();
        $result = array();
        if (count($querydata) > 0) {
            $result['start_sign_time'] = $querydata[0]['start_sign_time'];
            $result['end_sign_time'] = $querydata[0]['end_sign_time'];
        }
        return $result;
    }

    public function signIn($aid, $mid, $signTime)
    {
        $sign_in_record_model = new sign_in_record();
        $signTable = $this->getSignTable($aid, $mid);
        if (count($signTable) == 0) {
            try {
                $sign_in_record_model->create(["aid" => $aid, "start_sign_time" => $signTime, "end_sign_time" => null, "mid" => $mid]);
                return 1;
            } catch (\Illuminate\Database\QueryException $exception) {
                return 0;
            }
        } else {
            if ($sign_in_record_model->where('aid', $aid)->where('mid', $mid)->update(['end_sign_time' => $signTime])) {
                return 2;
            } else {
                return 0;
            }
        }
    }
}
