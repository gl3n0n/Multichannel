<?php

class Utils extends CApplicationComponent
{
	public function sendJSONResponse( $arr)
	{
		header('Content-type: application/json');
		echo CJSON::encode( $arr);
		Yii::app()->end();
	}

	public function getUserInfo($column)
	{
		return Users::model()->findByPk(Yii::app()->user->getId())->{$column};
	}

	public function send2Api($param=array())
	{
		$url     = trim($param['url']);
		$data    = $param['data'];
		//this is root-token-id: 0d754337454497646fea936fcd4695cdbaffb2626624a8eff4262457aef1977e
		$data['api_token'] = '0d754337454497646fea936fcd4695cdbaffb2626624a8eff4262457aef1977e';

		$opts    = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data),
				),
			   );
		//send it
		$context  = stream_context_create($opts);
		$result   = file_get_contents($url, false, $context);
		$retv     = json_decode($result, true);
		//give it back
		return $retv;
	}

	public function saveAuditLogs($mod=null,$attrs=null)
	{
		$model = new AuditLogs;
		$vPost = @var_export($_POST,true);
		$vGet  = @var_export($_GET, true);
		$vReq  = @var_export($_REQUEST,true);
		$vType = ((@count($_POST)) ? ('Post') : ('Get'));
		$vUrl  = Yii::app()->controller->getId().'/'.Yii::app()->controller->getAction()->getId();
		$vUrls = CHtml::normalizeUrl(array(Yii::app()->controller->getId().'/'.Yii::app()->controller->getAction()->getId()));
		$vQry  = @str_replace('&',"\n",@trim($_SERVER['QUERY_STRING']));
		$vPage = trim(strtoupper(Yii::app()->controller->getId()));
		$vAct  = trim(strtoupper(Yii::app()->controller->getAction()->getId()));
		
		$vAgent  = @trim($_SERVER['HTTP_USER_AGENT']);
		$vAgents = $this->checkBrowser();
		$vAgent  = @trim(sprintf("%s\n\n\n%s\n\n\n%s;",$vAgents['name'], $vAgents['version'], $vAgents['userAgent'] ));
		
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $vIP = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $vIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $vIP = $_SERVER['REMOTE_ADDR'];
		}

		$errCtr = 0;
		foreach(Yii::app()->user->getFlashes() as $KK => $message) {
			if(@preg_match("/(error|notice)/i",$KK))
				$errCtr++;
		}
		//chk
		if($mod != null)
		{
			if( $mod > 0)
			    $errCtr++;
		}
		// [ AuditId,ClientId,UserId,GetPost,UserType,UserAgent,IPAddr,UrlData,UrlQry,CreatedBy,DateCreated,]
		//put more attrs
		$model->setAttribute("UserId",    Yii::app()->user->id);
		$model->setAttribute("ClientId",  Yii::app()->user->ClientId);
		$model->setAttribute("GetPost",   $vType);
		$model->setAttribute("UserType",  Yii::app()->user->AccessType);
		$model->setAttribute("UserAgent", $vAgent);
		$model->setAttribute("IPAddr",    $vIP);
		$model->setAttribute("UrlData",   sprintf("URL:\n%s\nREQ:\n%s\n%s",$vUrls,$vReq,$mod));
		$model->setAttribute("UrlQry",    $attrs);

		$model->setAttribute("ModPage",   $vPage);
		$model->setAttribute("ModAction", $vAct);
		$model->setAttribute("LogDate", new CDbExpression('NOW()'));
		$reqCtr = (@count($_REQUEST)) ? (1) : (0);
		if(@preg_match("/(INSERT|CREATE|UPDATE|DELETE|APPROVE|GENERATE|NEW|CHANGE)/i",$vAct))
                {
		      if($errCtr <= 0 && $reqCtr>0)
                     	 $model->save();
                }
		
	}
	
	public function fmt_csv($cols=array())
	{
		//$hdr = sprintf('="CUSTOMER ID",="POINTS",="",');
		return sprintf('="%s",="",',join('",="',$cols));
	}
	
	public function push_xls_download($csv='')
	{
		//throw csv as download			
		header('Content-Description: File Transfer');
		header('Content-Type: application/msexcel');
		header('Content-Disposition: attachment; filename='.basename($csv));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '. filesize($csv));
		@flush();
		readfile($csv);
	
	}

	public function io_save($fname='', $body='', $mode = 'w')
	{
		//mode of fopen
		$mode  = @preg_match("/^(a|append)$/i", $mode) ? ('a') :  ('w');

		//open it
		$fh = fopen($fname, $mode);
		if($fh)
		{
			fwrite($fh, $body);
			fclose($fh); 
			$is_ok  = true;

		}

		//give it back ;-)
		return $is_ok;

	}

	                
                  
	function checkBrowser() 
	{ 
		$u_agent   = $_SERVER['HTTP_USER_AGENT']; 
		$bname     = 'Unknown';
		$platform  = 'Unknown';
		$version   = "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}

		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
		{ 
			$bname = 'Internet Explorer'; 
			$ub = "MSIE"; 
		} 
		elseif(preg_match('/Firefox/i',$u_agent)) 
		{ 
			$bname = 'Mozilla Firefox'; 
			$ub = "Firefox"; 
		} 
		elseif(preg_match('/Chrome/i',$u_agent)) 
		{ 
			$bname = 'Google Chrome'; 
			$ub = "Chrome"; 
		} 
		elseif(preg_match('/Safari/i',$u_agent)) 
		{ 
			$bname = 'Apple Safari'; 
			$ub = "Safari"; 
		} 
		elseif(preg_match('/Opera/i',$u_agent)) 
		{ 
			$bname = 'Opera'; 
			$ub = "Opera"; 
		} 
		elseif(preg_match('/Netscape/i',$u_agent)) 
		{ 
			$bname = 'Netscape'; 
			$ub = "Netscape"; 
		} 

		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
				')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}

		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version= $matches['version'][0];
			}
			else {
				$version= $matches['version'][1];
			}
		}
		else {
			$version= $matches['version'][0];
		}

		// check if we have a number
		if ($version==null || $version=="") {$version="?";}

		return array(
				'userAgent' => $u_agent,
				'name'      => $bname,
				'version'   => $version,
				'platform'  => $platform,
				'pattern'   => $pattern
				    );
		}

}
?>
