<?php

namespace Api;

use App\BottomLayerClass\apiAjax;
use App\Models\service_action;

class addServiceActionController extends apiAjax
{
    public function Run()
    {
        $name = $this->getParam('name');
        $startdate = $this->getParam('startdate');
        $enddate = $this->getParam('enddate');
        $type = $this->getParam('type');
        $number_people = $this->getParam('number_people');
        $sdFormatCheck = $this->dateFormatCheck($startdate, 'YYYY-MM-DD hh:mm:ss');
        $edFormatCheck = $this->dateFormatCheck($enddate, 'YYYY-MM-DD hh:mm:ss');

        if ($sdFormatCheck && $edFormatCheck && is_numeric($type) && $name != null && is_numeric($number_people)) {
            if ((strtotime($enddate) - strtotime($startdate)) / 60 >= 30) {
                if ($this->queryRepeatAction($name, $startdate, $enddate)) {
                    if ($this->insertAction($name, $startdate, $enddate, $type, $number_people)) {
                        return array();
                    } else {
                        $this->runError('新增活動失敗!');
                    }
                } else {
                    $this->runError('已有重複活動!');
                }
            } else {
                $this->runError('活動時間低於30分!');
            }
        } else {
            $this->runError('輸入格式錯誤!');
        }
    }

    public function insertAction($name, $startdate, $enddate, $type, $number_people)
    {
        $service_action_model = new service_action();
        try {
            $service_action_model->create([
                "name" => $name,
                "startdate" => $startdate,
                "enddate" => $enddate,
                "type" => $type,
                "number_people" => $number_people,
            ]);
        } catch (\Illuminate\Database\QueryException $exception) {
            return false;
        }
        return true;
    }

    public function queryRepeatAction($name, $startdate, $enddate)
    {
        $service_action_model = new service_action();
        $userdata = $service_action_model->where('name', $name)->where('startdate', $startdate)->where('enddate', $enddate)->get();

        if (count($userdata) == 0) {
            return true;
        } else {
            return false;
        }
    }
}
