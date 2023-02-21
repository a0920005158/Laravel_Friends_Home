<?php
namespace Bg\Api;

use App\BottomLayerClass\apiAjax;
use App\Models\member;

class getUserDataController extends apiAjax
{
    public function Run()
    {
        $userId = $this->getParam('userId');
        $data = $this->getUserDatax($userId);
        return $data;
    }

    public function getUserDatax($userId)
    {
        $member_model = new member();
        $mData = $member_model->where('line_user_id',$userId)->get();
        $result = array();
        foreach ($mData as $key => $value) {
            $result['mid'] = $value['mid'];
            $result['name'] = $value['name'];
        }
        return $result;
    }
}
