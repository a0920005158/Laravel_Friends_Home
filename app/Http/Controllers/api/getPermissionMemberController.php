<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\permission_member;

class getPermissionMemberController extends apiAjax
{
    public function Run()
    {
        $data = $this->getAllPermissionMems();
        if (count($data) > 0) {
            return $data;
        } else {
            $this->runError('發生錯誤!');
        }
    }

    public function getAllPermissionMems()
    {
        $userdata = permission_member::all();
        $result = array();

        if (count($userdata)) {
            foreach ($userdata as $key => $value) {
                $result[$key]['m_type'] = $value['m_type'];
                $result[$key]['permission'] = $value['permission'];
                $result[$key]['name'] = $value['name'];
            }
        }

        return $result;
    }
}
