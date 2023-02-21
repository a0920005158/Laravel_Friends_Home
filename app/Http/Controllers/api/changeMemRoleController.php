<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\permission_member;

class changeMemRoleController extends apiAjax
{
    public function Run()
    {
        $type = $this->getParam('type');
        $random = $this->getParam('random');
        $allPermissionType = $this->getAllPermissionMems();
        if (array_key_exists($type, $allPermissionType)) {
            $redis = getRedis();
            $redis->hset("userData:" . $random, 'type', $type);
            return array();
        } else {
            $this->runError('角色不存在!');
        }
    }

    public function getAllPermissionMems()
    {
        $userdata = permission_member::all();
        $result = array();

        if (count($userdata)) {
            foreach ($userdata as $key => $value) {
                $result[] = $value['m_type'];
            }
        }

        return $result;
    }
}
