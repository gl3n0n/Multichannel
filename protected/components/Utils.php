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

}
?>