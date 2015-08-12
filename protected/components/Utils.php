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

}
?>