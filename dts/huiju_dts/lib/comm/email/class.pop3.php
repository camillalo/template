<?php
// 
// 类名：SocketPOPClient 
// 功能：POP3 协议客户端的基本操作类 
//
class SocketPOP3Client {
    var $strMessage     = '';
    var $intErrorNum    = 0;
    var $bolDebug       = false; // 设置是否启用debug功能
    
    var $strEmail       = '';
    var $strPasswd      = '';
    var $strHost        = '';
    var $intPort        = "";
    var $sslPort        = "";
    var $intConnSecond  = 30;
    var $intBuffSize    = 8192;
 
    var $resHandler     = NULL;
    var $bolIsLogin     = false;
    var $strRequest     = '';
    var $strResponse    = '';
    var $arrRequest     = array();
    var $arrResponse    = array();
 
 
    //---------------
    // 基础操作
    //---------------
 
    //构造函数
    function  __construct($strLoginEmail, $strLoginPasswd, $strPopHost, $intPort='',$sslPort=''){
        $this->strEmail     = trim(strtolower($strLoginEmail));
        $this->strPasswd    = trim($strLoginPasswd);
        $this->strHost      = trim(strtolower($strPopHost));
        $this->intPort      = trim($intPort);
        $this->sslPort      = trim($sslPort);

        $this->connectHost();
    }
    
    //连接服务器
    function connectHost()
    {	

        if ($this->bolDebug)
        {
            echo "Connection ".$this->strHost." ...\r\n";
        }
        if (!$this->getIsConnect())
        {
            if ($this->strHost=='' || $this->intPort=='')
            {
                $this->setMessage('POP3 host or Port is empty', 1003);
                return false;            
            }

            if($this->sslPort!=''){
                $this->resHandler = @fsockopen("ssl://".$this->strHost,$this->sslPort,&$this->intErrorNum,&$this->strMessage,$this->intConnSecond);
            }else{ 
                $this->resHandler = @fsockopen($this->strHost,$this->intPort,&$this->intErrorNum,&$this->strMessage,$this->intConnSecond);
            }
            if (!$this->resHandler){
                $strErrMsg = 'Connection POP3 host: '.$this->strHost.' failed';
                $intErrNum = 2001;
                $this->setMessage($strErrMsg, $intErrNum);
                return false;
            }
            $this->getLineResponse();
            if (!$this->getRestIsSucceed())
            {
                return false;
            }
        }
        return true;
    }
 
    //关闭连接
    function closeHost()
    {
        if ($this->resHandler)
        {
            fclose($this->resHandler);
        }
        return true;
    }
 
    //发送指令
    function sendCommand($strCommand)
    {
        if ($this->bolDebug)
        {
            if (!preg_match("/PASS/", $strCommand))
            {
                echo "Send Command: ".$strCommand."\r\n";
            }
            else
            {
                echo "Send Command: PASS ******\r\n";
            }
 
        }
        if (!$this->getIsConnect())
        {
            return false;
        }
        if (trim($strCommand)=='')
        {
            $this->setMessage('Request command is empty', 1004);
            return false;
        }
        $this->strRequest = $strCommand."\r\n";
        $this->arrRequest[] = $strCommand;
        fputs($this->resHandler, $this->strRequest);
        return true;
    }
 
    //提取响应信息第一行
    function getLineResponse()
    {
        if (!$this->getIsConnect())
        {
            return false;
        }
        $this->strResponse = fgets($this->resHandler, $this->intBuffSize);
        $this->arrResponse[] = $this->strResponse;
 
        return $this->strResponse;        
    }
 
    //提取若干响应信息,$intReturnType是返回值类型, 1为字符串, 2为数组
    function getRespMessage($intReturnType)
    {
        if (!$this->getIsConnect())
        {
            return false;
        }
        if ($intReturnType == 1)
        {
            $strAllResponse = '';
            while(!feof($this->resHandler))
            {
                $strLineResponse = $this->getLineResponse();
                if (preg_match("/^\+OK/", $strLineResponse))
                {
                    continue;
                }
                if (trim($strLineResponse)=='.')
                {
                    break;
                }
                $strAllResponse .= $strLineResponse;
            }
            return $strAllResponse;
        }
        else
        {
            $arrAllResponse = array();
            while(!feof($this->resHandler))
            {
                $strLineResponse = $this->getLineResponse();
                if (preg_match("/^\+OK/", $strLineResponse))
                {
                    continue;
                }
                if (trim($strLineResponse)=='.')
                {
                    break;
                }
                $arrAllResponse[] = $strLineResponse;
            }
            return $arrAllResponse;            
        }
    }
 
    //提取请求是否成功
    function getRestIsSucceed($strRespMessage='')
    {
        if (trim($strRespMessage)=='')
        {
            if ($this->strResponse=='')
            {
                $this->getLineResponse();
            }
            $strRespMessage = $this->strResponse;
        }
        if (trim($strRespMessage)=='')
        {
            $this->setMessage('Response message is empty', 2003);
            return false;
        }
        if (!preg_match("/^\+OK/", $strRespMessage))
        {
            $this->setMessage($strRespMessage, 2000);
            return false;
        }
        return true;
    }
 
    //获取是否已连接
    function getIsConnect()
    {
        if (!$this->resHandler)
        {
            $this->setMessage("Nonexistent availability connection handler", 2002);
            return false;
        }
        return true;
    }
 
 
    //设置消息
    function setMessage($strMessage, $intErrorNum)
    {
        if (trim($strMessage)=='' || $intErrorNum=='')
        {
            return false;
        }
        $this->strMessage    = $strMessage;
        $this->intErrorNum    = $intErrorNum;
        return true;
    }
 
    //获取消息
    function getMessage()
    {
        return $this->strMessage;
    }
 
    //获取错误号
    function getErrorNum()
    {
        return $this->intErrorNum;
    }
 
    //获取请求信息
    function getRequest()
    {
        return $this->strRequest;        
    }
 
    //获取响应信息
    function getResponse()
    {
        return $this->strResponse;
    }
 
 
    //---------------
    // 邮件原子操作
    //---------------
 
    //登录邮箱
    function popLogin()
    {
        if (!$this->getIsConnect())
        {
            echo 'getIsConnect() is return false!<br>';
            return false;
        }
        $this->sendCommand("USER ".$this->strEmail);
        $this->getLineResponse();
        $bolUserRight = $this->getRestIsSucceed(); 
        $this->sendCommand("PASS ".$this->strPasswd);
        $this->getLineResponse();
        $bolPassRight = $this->getRestIsSucceed();
 
        if (!$bolUserRight || !$bolPassRight){
            $this->setMessage($this->strResponse, 2004);
            return false;
        }        
        $this->bolIsLogin = true;
        return true;
    }
 
    //退出登录
    function popLogout()
    {
        if (!$this->getIsConnect() && $this->bolIsLogin)
        {
            return false;
        }
        $this->sendCommand("QUIT");
        $this->getLineResponse();
        if (!$this->getRestIsSucceed())
        {
            return false;
        }
        return true;
    }
 
    //获取是否在线
    function getIsOnline()
    {
        if (!$this->getIsConnect() && $this->bolIsLogin)
        {
            return false;
        }
        $this->sendCommand("NOOP");
        $this->getLineResponse();
        if (!$this->getRestIsSucceed())
        {
            return false;
        }
        return true;        
    }
 
    //获取邮件数量和字节数(返回数组)
    function getMailSum($intReturnType=2){
        if (!$this->getIsConnect() && $this->bolIsLogin)
        {
            return false;
        }
        $this->sendCommand("STAT");
        $strLineResponse = $this->getLineResponse();
        if (!$this->getRestIsSucceed())
        {
            return false;
        }
        if ($intReturnType==1)
        {
            return     $this->strResponse;
        }
        else
        {
            $arrResponse = explode(" ", $this->strResponse);
            if (!is_array($arrResponse) || count($arrResponse)<=0)
            {
                $this->setMessage('STAT command response message is error', 2006);
                return false;
            }
            return array($arrResponse[1], $arrResponse[2]);
        }
    }
 
    //获取指定邮件的Session Id
    function getMailSessId($intMailId, $intReturnType=2)
    {
        if (!$this->getIsConnect() && $this->bolIsLogin)
        {
            return false;
        }
        if (!$intMailId = intval($intMailId))
        {
            $this->setMessage('Mail message id invalid', 1005);
            return false;
        }
        $this->sendCommand("UIDL ". $intMailId);
        $this->getLineResponse();
        if (!$this->getRestIsSucceed())
        {
            return false;
        }
        if ($intReturnType == 1)
        {
            return     $this->strResponse;
        }
        else
        {
            $arrResponse = explode(" ", $this->strResponse);
            if (!is_array($arrResponse) || count($arrResponse)<=0)
            {
                $this->setMessage('UIDL command response message is error', 2006);
                return false;
            }
            return array($arrResponse[1], $arrResponse[2]);
        }
    }
 
    //取得某个邮件的大小
    function getMailSize($intMailId, $intReturnType=2)
    {
        if (!$this->getIsConnect() && $this->bolIsLogin)
        {
            return false;
        }
        $this->sendCommand("LIST ".$intMailId);
        $this->getLineResponse();
        if (!$this->getRestIsSucceed())
        {
            return false;
        }
        if ($intReturnType == 1)
        {
            return $this->strResponse;
        }
        else
        {
            $arrMessage = explode(' ', $this->strResponse);
            return array($arrMessage[1], $arrMessage[2]);
        }
    }
 
    //获取邮件基本列表数组
    function getMailBaseList($intReturnType=2)
    {
        if (!$this->getIsConnect() && $this->bolIsLogin)
        {
            return false;
        }
        $this->sendCommand("LIST");
        $this->getLineResponse();
        if (!$this->getRestIsSucceed())
        {
            return false;
        }
        return $this->getRespMessage($intReturnType);
    }
 
    //获取指定邮件所有信息，intReturnType是返回值类型，1是字符串,2是数组
    function getMailMessage($intMailId, $intReturnType=2)
    {
        if (!$this->getIsConnect() && $this->bolIsLogin)
        {
            return false;
        }
        if (!$intMailId = intval($intMailId))
        {
            $this->setMessage('Mail message id invalid', 1005);
            return false;
        }
        $this->sendCommand("RETR ". $intMailId);
        $this->getLineResponse();
        if (!$this->getRestIsSucceed())
        {
            return false;
        }
        return $this->getRespMessage($intReturnType);
    }
 
    //获取某邮件前指定行, $intReturnType 返回值类型，1是字符串，2是数组
    function getMailTopMessage($intMailId, $intTopLines=10, $intReturnType=1)
    {
        if (!$this->getIsConnect() && $this->bolIsLogin)
        {
            return false;
        }
        if (!$intMailId=intval($intMailId) || !$intTopLines=int($intTopLines))
        {
            $this->setMessage('Mail message id or Top lines number invalid', 1005);
            return false;
        }
        $this->sendCommand("TOP ". $intMailId ." ". $intTopLines);
        $this->getLineResponse();
        if (!$this->getRestIsSucceed())
        {
            return false;
        }
        return $this->getRespMessage($intReturnType);
    }
 
    //删除邮件
    function delMail($intMailId)
    {
        if (!$this->getIsConnect() && $this->bolIsLogin)
        {
            return false;
        }
        if (!$intMailId=intval($intMailId))
        {
            $this->setMessage('Mail message id invalid', 1005);
            return false;
        }
        $this->sendCommand("DELE ".$intMailId);
        $this->getLineResponse();
        if (!$this->getRestIsSucceed())
        {
            return false;
        }
        return true;
    }
 
    //重置被删除得邮件标记为未删除
    function resetDeleMail()
    {
        if (!$this->getIsConnect() && $this->bolIsLogin)
        {
            return false;
        }
        $this->sendCommand("RSET");
        $this->getLineResponse();
        if (!$this->getRestIsSucceed())
        {
            return false;
        }
        return true;        
    }

    //解码
	function decode_mime($string) {
		$pos = strpos($string, '=?');
		if (!is_int($pos)) {
			return $string;
		}
		$preceding = substr($string, 0, $pos); // save any preceding text
		$search = substr($string, $pos+2); /* the mime header spec says this is the longest a single encoded word can be */
		$d1 = strpos($search, '?');
		if (!is_int($d1)) {
			return $string;
		}
		$charset = substr($string, $pos+2, $d1); //取出字符集的定义部分
		$search = substr($search, $d1+1); //字符集定义以后的部分＝>$search;
		$d2 = strpos($search, '?');
		if (!is_int($d2)) {
			return $string;
		}
		$encoding = substr($search, 0, $d2); ////两个?　之间的部分编码方式　：ｑ　或　ｂ　
		$search = substr($search, $d2+1);
		$end = strpos($search, '?='); //$d2+1 与 $end 之间是编码了　的内容：=> $endcoded_text;
		if (!is_int($end)) {
			return $string;
		}
		$encoded_text = substr($search, 0, $end);
		$rest = substr($string, (strlen($preceding . $charset . $encoding . $encoded_text)+6)); //+6 是前面去掉的　=????=　六个字符
		switch ($encoding) {
			case 'Q':
			case 'q':
			//$encoded_text = str_replace('_', '％20', $encoded_text);
			//$encoded_text = str_replace('=', '％', $encoded_text);
			//$decoded = urldecode($encoded_text);
			$decoded=quoted_printable_decode($encoded_text);
			if (strtolower($charset) == 'windows-1251') {
			$decoded = convert_cyr_string($decoded, 'w', 'k');
			}
			break;
			case 'B':
			case 'b':
			$decoded = base64_decode($encoded_text);
			if (strtolower($charset) == 'windows-1251') {
			$decoded = convert_cyr_string($decoded, 'w', 'k');
			}
			break;
			default:
			$decoded = '=?' . $charset . '?' . $encoding . '?' . $encoded_text . '?=';
			break;
		}
		return $preceding . $decoded . $this->decode_mime($rest);
	}
 
 
 
    //---------------
    // 调试操作
    //---------------
 
    //输出对象信息
    function printObject()
    {
        print_r($this);
        exit;
    }
 
    //输出错误信息
    function printError()
    {
        echo "[Error Msg] : $strMessage     <br>\n";
        echo "[Error Num] : $intErrorNum <br>\n";
        exit;
    }
 
    //输出主机信息
    function printHost()
    {
        echo "[Host]  : $this->strHost <br>\n";
        echo "[Port]  : $this->intPort <br>\n";
        echo "[Email] : $this->strEmail <br>\n";
        echo "[Passwd] : ******** <br>\n";
        exit;
    }
 
    //输出连接信息
    function printConnect()
    {
        echo "[Connect] : $this->resHandler <br>\n";
        echo "[Request] : $this->strRequest <br>\n";
        echo "[Response] : $this->strResponse <br>\n";
        exit;
    }
}

	/**
	**链接数据库
	**/
	function relate_db($host,$user,$password,$db){ 
		$conn = mysql_connect($host,$user,$password) or die(mysql_error());
		mysql_select_db($db) or die(mysql_error());
		mysql_query("set names 'utf8'");
		return "success";
	}
?>