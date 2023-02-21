<?php
namespace App\BottomLayerClass;

use App\BottomLayerClass\paramHandle;
use DateTime;
use Log;

abstract class apiAjax extends paramHandle
{
    private $userData = array();
    private $random = "";
    private $logPath = '/log/'; //資料夾路徑
    private $request = null;

    public function __construct()
    {
        $this->logPath = dirname(dirname(__FILE__)) . $this->logPath;
    }

    abstract public function Run();

    public function runError($msg)
    {
        $errorMsg = array();
        $errorMsg['callState'] = 1;
        $errorMsg['errorMsg'] = $msg;
        $errorMsg['response'] = array();
        $errorMsg['random'] = $this->random;
        echo json_encode($errorMsg);
        exit();
    }

    public function dateFormatCheck($datetime, $format)
    {
        $f = "YYYY-MM-DD HH:MM:SS";
        $DT = explode(" ", $format);
        $rule = "/^";
        foreach ($DT as $key => $value) {
            if (strpos($value, "-") !== false) {
                $DTS = explode("-", $value);
            } else {
                $DTS = explode(":", $value);
            }

            foreach ($DTS as $key2 => $value2) {
                switch ($value2) {
                    case 'YYYY':
                        $rule .= "[0-9]{4}";
                        break;
                    case 'MM':
                        $rule .= "(0[1-9]|1[0-2])";
                        break;
                    case 'DD':
                        $rule .= "(0[1-9]|[1-2][0-9]|3[0-1])";
                        break;
                    case 'hh':
                        $rule .= "(0[0-9]|1[0-9]||2[0-3])";
                        break;
                    case 'mm':
                        $rule .= "([0-5][0-9])";
                        break;
                    case 'ss':
                        $rule .= "([0-5][0-9])";
                        break;
                }
                if ($key2 != count($DTS) - 1) {
                    if ($value2 == 'YYYY' || $value2 == 'MM' || $value2 == 'DD') {
                        $rule .= "-";
                    } else {
                        $rule .= ":";
                    }
                }
            }

            if (count($DT) == 2 && $key == 0) {
                $rule .= "[\s]";
            }
        }
        $rule .= "$/";

        if (preg_match($rule, $datetime)) {
            return true;
        } else {
            return false;
        }
    }

    public function encryption($password)
    {
        return md5($password);
    }

    public function setRandom($random)
    {
        if (gettype($random) == "string") {
            $this->random = $random;
        }
    }

    public function setRequest($request){
        $this->request = $request;
    }

    public function setUserData($data)
    {
        $dataLimit = 'mid|acc|job|joinDate|type|line|phone|birthDay|name|userId';
        $isAuthSuccess = true;
        foreach ($data as $key => $value) {
            if (strpos($dataLimit, $key) !== false) {
                $this->userData[$key] = $value;
            } else {
                $isAuthSuccess = false;
            }
        }
        return $isAuthSuccess;
    }

    public function getUserData()
    {
        return $this->userData;
    }

    public function PostData_LineNotify($Token, $message)
    {
        $headers = array(
            'Content-Type: multipart/form-data',
            'Authorization: Bearer ' . $Token,
        );
        $message = array(
            'message' => $message,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $content = json_decode($result);
        $content_arr = $this->objtoarr($content);

        curl_close($ch);
        return $content_arr;
    }

    public function PostData_LineNotify_Img($Token, $url, $msg)
    {
        $headers = array(
            'Content-Type: multipart/form-data',
            'Authorization: Bearer ' . $Token,
        );
        $message = array(
            'message' => $msg,
            'imageThumbnail' =>$url,
            'imageFullsize'=> $url,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $content = json_decode($result);
        $content_arr = $this->objtoarr($content);

        curl_close($ch);
        return $content_arr;
    }

    public function objtoarr($obj)
    {
        $ret = array();
        foreach ($obj as $key => $value) {
            if (gettype($value) == 'array' || gettype($value) == 'object') {
                $ret[$key] = $this->objtoarr($value);
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    public function encode($data)
    {
        return str_replace(array(' ', '/', '='), array('-', '_', ''), base64_encode(serialize($data)));
    }

    public function decode($string)
    {
        $data = str_replace(array('-', '_'), array(' ', '/'), $string);
        $mod4 = strlen($data) % 4;
        ($mod4) && $data .= substr('====', $mod4);
        return unserialize(base64_decode($data));
    }

    public function curl_post($url, $data = "")
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        // var_dump($resp);

        return json_decode($resp);
    }

    public function write($message, $fileSalt)
    {
        $date = new DateTime(); //現在時間
        Log::info($date->format('H:i:s').' '.$message);
        // $log = $this->logPath . $date->format('Y-m-d') . "-" . hash('sha512', $date->format('Y-m-d') . $fileSalt) . ".txt"; //檔案位置，使用日期與識別碼做SHA512當做檔名
        // if (is_dir($this->logPath)) { //判斷檔案資料夾是否存在
        //     if (!file_exists($log)) { //判斷檔案是否存在
        //         $fh = fopen($log, 'a+') or die("Fatal Error !"); //建立文件
        //         $logcontent = "Time : " . $date->format('H:i:s') . "\r\n" . $message . "\r\n"; //要存的文字
        //         fwrite($fh, $logcontent); //寫入
        //         fclose($fh); //關閉
        //     } else { //如果存再，就用覆寫的方式edit()
        //         $this->edit($log, $date, $message);
        //     }
        // } else { //資料夾不存在，所以建立資料夾後，再次呼叫write()
        //     if (mkdir($this->logPath, 0777) === true) {
        //         $this->write($message, $fileSalt);
        //     }
        // }
    }
    private function edit($log, $date, $message)
    {
        $logcontent = "Time : " . $date->format('H:i:s') . "\r\n" . $message . "\r\n\r\n"; //要記錄得文字
        $logcontent = $logcontent . file_get_contents($log); //添加在最前面
        file_put_contents($log, $logcontent); //上傳
    }
}
