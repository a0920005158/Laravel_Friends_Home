<?php
include_once dirname(dirname(__FILE__)) . '/_Sys/config_redis.php';
class LineLogin
{
    private $client_id = "";
    private $redirect_uri = "";
    private $client_secret = "";
    public function __construct($client_id, $client_secret, $redirect_uri)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }
    public function createLoginUrl($state, $scope = "profile%20openid")
    {
        $url = "https://access.line.me/oauth2/v2.1/authorize?";
        $param = array();
        $param["response_type"] = "code";
        $param["client_id"] = $this->client_id; //"1655872654";
        $param["redirect_uri"] = $this->redirect_uri; //"https://lin757tw.synology.me:8083/Line_LoginCallback.php";
        $param["state"] = $state;
        $param["scope"] = $scope;
        return $url . urldecode(http_build_query($param));
    }

    public function getTokenByCode($code)
    {
        $url = "https://api.line.me/oauth2/v2.1/token";
        $arr = array();
        $arr["grant_type"] = "authorization_code"; //"refresh_token";
        $arr["code"] = $code; //refresh_token
        $arr["redirect_uri"] = $this->redirect_uri;
        $arr["client_id"] = $this->client_id;
        $arr["client_secret"] = $this->client_secret;
        $re = $this->iCURL($url, $arr, $header);
        return json_decode($re, true);
    }

    public function getTokenByRefreshToken($refresh_token)
    {
        $url = "https://api.line.me/oauth2/v2.1/token";
        $arr = array();
        $arr["grant_type"] = "refresh_token";
        $arr["code"] = $refresh_token;
        $arr["redirect_uri"] = $this->redirect_uri;
        $arr["client_id"] = $this->client_id;
        $arr["client_secret"] = $this->client_secret;
        $re = $this->iCURL($url, $arr, $header);
        return json_decode($re, true);

    }

    public function decodeID_Token($jwt)
    {
        $leeway = 0;
        $timestamp = time();
        $tks = \explode('.', $jwt);
        if (\count($tks) != 3) {
            throw new UnexpectedValueException('Wrong number of segments');
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        if (null === ($header = json_decode(static::urlsafeB64Decode($headb64), false, 512, JSON_BIGINT_AS_STRING))) {
            throw new UnexpectedValueException('Invalid header encoding');
        }
        if (null === $payload = json_decode(static::urlsafeB64Decode($bodyb64), false, 512, JSON_BIGINT_AS_STRING)) {
            throw new UnexpectedValueException('Invalid claims encoding');
        }
        if (false === ($sig = static::urlsafeB64Decode($cryptob64))) {
            throw new UnexpectedValueException('Invalid signature encoding');
        }
        if (empty($header->alg)) {
            throw new UnexpectedValueException('Empty algorithm');
        }

        if (isset($payload->nbf) && $payload->nbf > ($timestamp + $leeway)) {
            echo '>>>>>';
            print_r("Error1");
            echo '<br>';
        }

        if (isset($payload->iat) && $payload->iat > ($timestamp + $leeway)) {
            echo '>>>>>';
            print_r("Error2");
            echo '<br>';
        }

        if (isset($payload->exp) && ($timestamp - $leeway) >= $payload->exp) {
            echo '>>>>>';
            print_r("Expired token");
            echo '<br>';
        }
        return $payload;
    }

    public function verifyToken($id_token)
    {
        $url = "https://api.line.me/oauth2/v2.1/verify";
        $arr = array();
        $arr["id_token"] = $id_token;
        $arr["client_id"] = $this->client_id;
        $re = $this->iCURL($url, $arr, $header);
        $exp = $re["exp"]; //Token 的有效期限(UNIX 時間)
        $iat = $re["iat"]; //ID token 產生的時間 (UNIX 時間)。
        $email = $re["email"];
        return json_decode($re, true);
    }

    private function iCURL($url, $arr, &$header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arr));
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        curl_close($ch);
        return $body;
    }

    private static function urlsafeB64Decode($input)
    {
        $remainder = \strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= \str_repeat('=', $padlen);
        }
        return \base64_decode(\strtr($input, '-_', '+/'));
    }

    // --------------------Joan-----------------------

    public function verifyAccToken($access_token)
    { //joan新增
        $url = "https://api.line.me/oauth2/v2.1/verify";
        $arr = array();
        $arr["access_token"] = $access_token;
        $re = $this->iCURL($url, $arr, $header);
        if (isset($re["expires_in"]) && $re["expires_in"] == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function accTokenGetProfile($access_token)
    { //joan新增
        $url = "https://api.line.me/v2/profile";
        $curl_h = curl_init($url);

        curl_setopt($curl_h, CURLOPT_HTTPHEADER,
            array(
                'Authorization: Bearer ' . $access_token,
            )
        );
        curl_setopt($curl_h, CURLOPT_RETURNTRANSFER, true);

        $re = curl_exec($curl_h);

        return json_decode($re, true);
    }

    public function reAuthPermission()
    {
        $newRandom = md5(time());
        $redis = getRedis();
        $redis->set("lineState:" . $newRandom, '1');
        $url = $this->createLoginUrl($newRandom);
        $this->urlRedir($url);
    }

    public function errorRedir()
    {
        echo '發生錯誤!請通知管理員處理';
        exit();
        $this->urlRedir('https://ccf.bllin.net/UI/admin.html');
    }

    public function urlRedir($url)
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $url);
    }

}
