<?php
/**
 * wechat php test
 */

//define your token
define("TOKEN", "addamx");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
//开启自动回复
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        //进行用户数字签名验证
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)) {
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
            the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            //通过simpleXML解析 "LIBXML_NOCDATA":Merge CDATA as text nodes
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //手机端
            $fromUsername = $postObj->FromUserName;
            //微信公众平台
            $toUsername = $postObj->ToUserName;
            //接收用户发送的关键词
            $keyword = trim($postObj->Content);
            //*接收用户消息类型
            $msgType = $postObj->MsgType;
            $time    = time();
            //文本模板
            $textTpl = <<<'EOF'
            <xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>
EOF;
            $musicTpl = <<<'EOF'
            <xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Music>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <MusicUrl><![CDATA[%s]]></MusicUrl>
            <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
            </Music>
            </xml>
EOF;
            if ($msgType == 'text') {
                if ($keyword == "文本") {
                    //回复类型，如果是text，代表文本类型
                    $msgType = "text";
                    //回复内容
                    $contentStr = "接受文本信息成功";
                    $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                } elseif ($keyword == "?" || $keyword == '？') {
                    $msgType    = 'text';
                    $contentStr = "[1]特种服务号码\n[2]银行服务号码\n[3]通讯服务号码\n请输入[]方括号的编号获取内容";
                    $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                } elseif ($keyword == '1') {
                    $msgType    = 'text';
                    $contentStr = "特种服务号码: 警察110, 火警119";
                    $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                } elseif ($keyword == '音乐' || $keyword == 'mp3') {
                    $msgType = 'music';
                    $title == '骑士王の夸り';
                    $desc == '<<骑士王の夸り>>';
                    $url == 'http://112.90.246.62/m10.music.126.net/20160710153602/3e9f7798a519e29b651173224432e574/ymusic/a9bd/4dfa/11ff/438db50d5ab513f11f26d6021801460a.mp3';
                    $hqurl == 'hhttp://112.90.246.62/m10.music.126.net/20160710153602/3e9f7798a519e29b651173224432e574/ymusic/a9bd/4dfa/11ff/438db50d5ab513f11f26d6021801460a.mp3';
                    $resultStr = sprintf($musicTpl, $fromUsername, $toUsername, $time, $msgType, $title, $desc, $url, $hqurl);

                    $msgType    = 'text';
                    $contentStr = $resultStr;
                    $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                } else {
                    $msgType    = 'text';
                    $contentStr = "输入无效";
                    $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                }

                //格式文本

                echo $resultStr;

            } elseif ($msgType == 'image') {
                if (!empty($keyword)) {
                    //回复类型，如果是text，代表文本类型
                    $msgType = "image";
                    //回复内容
                    $contentStr = "你发送的是图片";
                    //格式文本
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    //将XML消息返回公众平台
                    echo $resultStr;
                }
            }

        } else {
            echo "no post data";
            exit;
        }
    }

    //定义checkSingature
    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];

        $token  = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

}
