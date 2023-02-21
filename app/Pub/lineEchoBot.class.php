<?php
require_once dirname(__FILE__) . '/LINEBotTiny.php';
class lineEchoBot
{
    private $channelAccessToken = "";
    private $channelSecret = "";
    private $client = null;
    private $event = null;
    private $sourceData = array("userId" => "", "type" => "");

    public function __construct($channelAccessToken, $channelSecret)
    {
        $this->channelAccessToken = $channelAccessToken;
        $this->channelSecret = $channelSecret;
        $this->client = new LINEBotTiny($channelAccessToken, $channelSecret);
        $this->event = $this->client->parseEvents()[0];
        $this->sourceData["userId"] = $this->event['source']['userId'];
        $this->sourceData["type"] = $this->event['source']['type'];
    }

    public function getMsg()
    {
        $this->event = $this->client->parseEvents()[0];
        if ($this->event['type'] == 'message') {
            if ($this->event['message']['type'] == 'text') {
                if (paramCheck($this->event['message']['text'])) {
                    return $this->event['message']['text'];
                } else {
                    error_log('paramCheck error');
                }
            } else {
                error_log('Unsupported message type: ' . $this->event['type']);
            }
        } else if ($this->event['type'] == 'beacon') {
            $beacon = $this->event['beacon'];
            $beacon_hwid = $beacon["hwid"];
            $beacon_type = $beacon["type"];
            return "/beacon%" . $beacon_hwid . "%type" . $beacon_type;
        } else {
            error_log('Unsupported event type: ' . $this->event['type']);
        }
    }

    public function replyMessage($replyMsg)
    {
        $replyMsgData = [
            [
                'type' => 'text',
                'text' => $replyMsg,
            ],
        ];

        $this->client->replyMessage([
            'replyToken' => $this->event['replyToken'],
            'messages' => $replyMsgData,
        ]);
    }

    public function getUserId()
    {
        return $this->sourceData['userId'];
    }

    public function paramCheck($str)
    {
        $len_OLD = mb_strlen($str, 'utf-8');
        $tmp = strtolower($str);

        $pregStr = 'select|insert|update|delete|union|into|load_file|outfile|script|drop|http|truncate|having|shutdown';
        $w = explode('|', $pregStr);

        for ($i = 0; $i < sizeof($w); $i++) {
            $tmp = str_replace($w[$i], '', $tmp);
        }

        $len_NEW = mb_strlen($tmp, 'utf-8');

        if ($len_OLD != $len_NEW) {
            return false;
        }

        return true;
    }
}
