<?php
namespace App\Http\Controllers;

header('Access-Control-Allow-Origin: *'); // 讓vue rwd可以跨網域呼叫

use App\Models\member;
use App\Models\service_action;
use Illuminate\Http\Request;

class actionInviteController
{
    public function Run(Request $request)
    {
        $userId = $request->get("userId");
        $name = $request->get("name");
        $data = $this->getActionListData($name,$userId);
        // return view('actionInvite',[
        //     'action' => $data,
        // ]);
        return $data;
    }

    public function getActionListData($name,$userId)
    {
        $mData = member::firstWhere('line_user_id', $userId);
        
        if($mData == null){
            $mid = $this->createAcc($name, $userId);
            if ($mid) {
                $mData = member::firstWhere('line_user_id', $userId);
            }
        }
        
        $today = date("Y-m-d H:i:s");
        $service_action_model = new service_action();
        $service = $service_action_model->where('startdate', '>', $today)->get();
        $result = array();
        
        if (count($service) > 0 && $mData != null) {
            foreach ($service as $key => $value) {
                $nowMemCount = $value['actionmem'] == null || $value['actionmem'] == "" ? 0 : count(explode(",", $value['actionmem']));
                if ($nowMemCount < $value['number_people']) {
                    $result[$key]['name'] = $value['name'];
                    $result[$key]['startdate'] = substr($value['startdate'], 0, -3);
                    $result[$key]['enddate'] = substr($value['enddate'], 0, -3);
                    $result[$key]['actionmem'] = $value['actionmem'];
                    $result[$key]['principal'] = $value['principal'];
                    $result[$key]['aid'] = $value['aid'];
                    $result[$key]['number_people'] = $value['number_people'];
                    $result[$key]['is_invite'] = in_array($mData['mid'], explode(",", $value['actionmem']));
                }
            }
        }

        return array('actionArr'=>$result,'is_bind_line'=>($mData['line']!== ""&&isset($mData['line'])));
    }

    public function createAcc($name, $userId)
    {
        if (isset($name) && isset($userId)) {
            $member_model = new member();
            $haveMem = $member_model->where('name', $name)->where('line_user_id', $userId)->get();
            if (count($haveMem) == 0) {
                try {
                    $member_model->create([
                        'name' => $name,
                        'line_user_id' => $userId,
                        'pass' => 1
                    ]);
                    return true;
                } catch (\Illuminate\Database\QueryException $exception) {
                    return false;
                }
            }
        }
        return false;
    }
}
