<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\permission;

class getPermissionController extends apiAjax
{
    public function Run()
    {
        $data = $this->getAllPermission();
        return $data;
    }

    public function getAllPermission()
    {
        $result = permission::all();

        return $result;
    }
}
