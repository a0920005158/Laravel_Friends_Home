<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\members_sign_table;
use App\Models\service_action;
use App\Models\sign_in_record;

class makeUpSignInController extends apiAjax
{
    public function Run()
    {
        $aid = $this->getParam('aid');
        $mid = $this->getParam('mid');
        $op = $this->getParam('op');
        $time = $this->getParam('time');
        if ($this->midActionCheck($aid, $mid)) {
            if ($op == 'signIn' || $op == 'signOut') {
                $makeUpSignId = $this->makeUpSign($aid, $mid, $op, $time);
                if ($makeUpSignId != null) {
                    $r = $this->getSign($makeUpSignId);
                    if ($r != null) {
                        return $r;
                    } else {
                        $this->runError('發生錯誤!');
                    }
                } else {
                    $this->runError('補簽到失敗!');
                }
            } else {
                $this->runError('格式錯誤!');
            }
        } else {
            $this->runError('會員未參加此活動!');
        }
    }

    public function midActionCheck($aid, $mid)
    {
        $sqlResult = service_action::firstWhere('aid', $aid);
        $result = false;
        if ($sqlResult != null) {
            $isInvite = in_array($mid, explode(",", $sqlResult['actionmem']));
            if ($isInvite) {
                $result = true;
            }
        }
        return $result;
    }

    public function getSign($sirid)
    {
        return members_sign_table::firstWhere('sirid', $sirid);
    }

    public function makeUpSign($aid, $mid, $op, $time)
    {
        $sign_in_record_model = new sign_in_record();
        $sqlResult = $sign_in_record_model->where('aid', $aid)->where('mid', $mid)->get();
        $sirid = null;
        if (count($sqlResult) > 0) {
            $sql2 = $sign_in_record_model->where('sirid', $sqlResult[0]['sirid']);
            // if ($op == 'signIn' && ((strtotime($time) < strtotime($sqlResult[0]['end_sign_time'])) || $sqlResult[0]['end_sign_time'] == null || $sqlResult[0]['end_sign_time'] == ""))
            if ($op == 'signIn' && ((strtotime($time) < strtotime($sqlResult[0]['end_sign_time']) || $sqlResult[0]['end_sign_time'] == NULL))) {
                $updateResult = $sql2->update(['start_sign_time' => $time]);
                // else if ($op == 'signOut' && ((strtotime($time) > strtotime($sqlResult[0]['start_sign_time'])) || $sqlResult[0]['start_sign_time'] == null || $sqlResult[0]['start_sign_time'] == ""))
            } else if ($op == 'signOut' && (strtotime($time) > strtotime($sqlResult[0]['start_sign_time']))) {
                $updateResult = $sql2->update(['end_sign_time' => $time]);
            } else {
                return null;
            }

            if ($updateResult) {
                $sirid = $sqlResult[0]['sirid'];
            }
        } else {
            if ($op == 'signIn') {
                try {
                    $sql3Result = $sign_in_record_model->create([
                        'aid' => $aid,
                        'start_sign_time' => $time,
                        'end_sign_time' => null,
                        'mid' => $mid,
                    ]);
                    $sirid = $sql3Result->id;
                } catch (\Illuminate\Database\QueryException $exception) {
                    return null;
                }
            } else {
                try {
                    $sql3Result = $sign_in_record_model->create([
                        'aid' => $aid,
                        'start_sign_time' => null,
                        'end_sign_time' => $time,
                        'mid' => $mid,
                    ]);
                    $sirid = $sql3Result->id;
                } catch (\Illuminate\Database\QueryException $exception) {
                    return null;
                }
            }
        }
        return $sirid;
    }
}
