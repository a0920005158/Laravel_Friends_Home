<?php
namespace App\_Pub;

require_once dirname(__FILE__) . '/LINEBotTiny.php';
// use App\_Pub\LINEBotTiny;

class lineEchoBot
{
    private $channelAccessToken = "";
    private $channelSecret = "";
    private $client = null;
    private $event = null;

    public function __construct($channelAccessToken, $channelSecret)
    {
        $this->channelAccessToken = $channelAccessToken;
        $this->channelSecret = $channelSecret;
        $this->client = new LINEBotTiny($channelAccessToken, $channelSecret);
        $this->event = $this->client->parseEvents();
    }

    public function getMsg()
    {
        $msgArr = array();
        foreach ($this->event as $event) {
            switch ($event['type']) {
                case 'message':
                    $message = $event['message'];
                    if ($message['type'] == 'text') {
                        if ($this->paramCheck($message['text'])) {
                            array_push($msgArr,
                                array(
                                    "text" => $message['text'],
                                    "userId" => $event['source']['userId'],
                                    "type" => $event['source']['type'],
                                    "replyToken" => $event["replyToken"],
                                )
                            );
                        } else {
                            error_log('paramCheck error');
                        }
                    } else {
                        error_log('Unsupported message type: ' . $this->event['type']);
                    }
                    break;

                case 'beacon':
                    $beacon = $event['beacon'];
                    $beacon_hwid = $beacon["hwid"];
                    $beacon_type = $beacon["type"];
                    array_push($msgArr,
                        array(
                            "text" => "/beacon%" . $beacon_hwid . "%type" . $beacon_type,
                            "userId" => $event['source']['userId'],
                            "type" => $event['source']['type'],
                            "replyToken" => $event["replyToken"],
                        )
                    );
                    break;
                default:
                    error_log('Unsupported event type: ' . $event['type']);
                    break;
            }
        };
        return $msgArr;
    }

    public function replyMessage($replyMsg, $replyToken)
    {
        $replyMsgData = [
            [
                'type' => 'text',
                'text' => $replyMsg,
            ],
        ];

        $this->client->replyMessage([
            'replyToken' => $replyToken,
            'messages' => $replyMsgData,
        ]);
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
