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
            //通过simpleXML解析
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
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if ($msgType == 'text') {
                if (!empty($keyword)) {
                    //回复类型，如果是text，代表文本类型
                    $msgType = "text";
                    //回复内容
                    $contentStr = "你发送的文本消息";
                    //格式文本
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    //将XML消息返回公众平台
                    echo $resultStr;
                } elseif ($keyword == "?" || $keyword == '？') {
                    $msgType    = 'text';
                    $contentStr = '[1]特种服务号码\n[2]银行服务号码\n[3]通讯服务号码\n请输入[]方括号的编号获取内容';
                    $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                } else {
                    echo "Input something...";
                }
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
                } else {
                    echo "Input something...";
                }
            }

        } else {
            echo "";
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
