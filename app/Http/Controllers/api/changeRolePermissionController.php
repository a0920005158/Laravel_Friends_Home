<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\permission_member;

class changeRolePermissionController extends apiAjax
{
    public function Run()
    {
        $m_type = $this->getParam('m_type');
        $permission = $this->getParam('permission');
        $callApi = $this->getParam('callApi');
        if ($this->permissionFormatCheck($permission) && $this->callApiFormatCheck($callApi)) {
            if ($this->updateRolePermissionD($m_type, $permission, $callApi)) {
                $redis = getRedis();
                $redis->hset("role_power", $m_type, $callApi);
                return array();
            } else {
                $this->runError('角色權限修改失敗!');
            }
        } else {
            $this->runError('格式錯誤!');
        }
    }

    public function permissionFormatCheck($data)
    {
        $pattern = "/^\d{1}(,\d{1,}){0,}$/";
        $result = preg_match($pattern, $data);
        return $result;
    }

    public function callApiFormatCheck($data)
    {
        $pattern = "/^\w{1,}(,\w{1,}){0,}$/";
        $result = preg_match($pattern, $data);
        return $result;
    }

    public function updateRolePermissionD($m_type, $permission, $callApi)
    {
        $permission_member_model = new permission_member();
        return $permission_member_model->where('m_type', $m_type)->update(['permission' => $permission, 'callApi' => $callApi]);
    }
}
