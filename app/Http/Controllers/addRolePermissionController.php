<?php

namespace App\Http\Controllers;

use App\BottomLayerClass\apiAjax;
use App\Models\permission_member;

class addRolePermissionController extends apiAjax
{
    public function Run()
    {
        $permission = $this->getParam('permission');
        $name = $this->getParam('name');
        $callApi = $this->getParam('callApi');
        if ($this->permissionFormatCheck($permission) && $this->callApiFormatCheck($callApi)) {
            $permissionId = $this->addRolePermissionD($permission, $name, $callApi);
            if ($permissionId) {
                $redis = getRedis();
                $redis->hset("role_power", $permissionId, $callApi);
                return array();
            } else {
                $this->runError('角色權限新增失敗!');
            }
        } else {
            $this->runError('格式錯誤!');
        }
    }

    public function permissionFormatCheck($data)
    {
        $pattern = "/^\d{1}(,\d){0,}$/";
        $result = preg_match($pattern, $data);
        return $result;
    }

    public function callApiFormatCheck($data)
    {
        $pattern = "/^\w{1,}(,\w{1,}){0,}$/";
        $result = preg_match($pattern, $data);
        return $result;
    }

    public function addRolePermissionD($permission, $name, $callApi)
    {
        try {
            $result = permission_member::create([
                'permission' => $permission,
                'name' => $name,
                'callApi' => $callApi,
            ]);
        } catch (\Illuminate\Database\QueryException $exception) {
            return false;
        }
        return $result['id'];
    }
}