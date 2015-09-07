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

	public function saveAuditLogs()
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
		
		$vAgent= @trim($_SERVER['HTTP_USER_AGENT']);
		
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $vIP = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $vIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $vIP = $_SERVER['REMOTE_ADDR'];
		}
		
		// [ AuditId,ClientId,UserId,GetPost,UserType,UserAgent,IPAddr,UrlData,UrlQry,CreatedBy,DateCreated,]
		//put more attrs
		$model->setAttribute("UserId",    Yii::app()->user->id);
		$model->setAttribute("ClientId",  Yii::app()->user->ClientId);
		$model->setAttribute("GetPost",   $vType);
		$model->setAttribute("UserType",  Yii::app()->user->AccessType);
		$model->setAttribute("UserAgent", $vAgent);
		$model->setAttribute("IPAddr",    $vIP);
		$model->setAttribute("UrlData",   sprintf("URL:\n%s\nREQ:\n%s\n",$vUrls,$vReq));
		$model->setAttribute("UrlQry",    $vQry);

		$model->setAttribute("ModPage",   $vPage);
		$model->setAttribute("ModAction", $vAct);
		$model->setAttribute("LogDate", new CDbExpression('NOW()'));
		
		$model->save();
	}
}
?>