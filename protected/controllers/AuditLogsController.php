<?php


class AuditLogsController extends Controller
{

	public $extraJS;
	public $mainDivClass;
	public $modals;
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('index','view','create','update','list','admin','delete','csv'),
				'users'  =>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

 

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new AuditLogs;
		$vPost = @print_r($_POST);
		$vGet  = @print_r($_GET);
		$vType = (isset($_POST) ? ('Post') : ('Get'));
		$vUrl  = Yii::app()->controller->getId().'/'.Yii::app()->controller->getAction()->getId();
		$vIP   = Yii::app()->request->getUserHostAddress();
		$vQry  = Yii::app()->request->getQueryString();
		$vAgent= Yii::app()->request->getUserAgent();
		
		
		// [ AuditId,ClientId,UserId,GetPost,UserType,UserAgent,IPAddr,UrlData,UrlQry,CreatedBy,DateCreated,]
		//put more attrs
		$model->setAttribute("UserId",    Yii::app()->user->id);
		$model->setAttribute("ClientId",  Yii::app()->user->ClientId);
		$model->setAttribute("GetPost",   $vType);
		$model->setAttribute("UserType",  Yii::app()->user->AccessType);
		$model->setAttribute("UserAgent", $vAgent);
		$model->setAttribute("IPAddr",    $vIP);
		$model->setAttribute("UrlData",   sprintf("%s\n%s",$vPost,$vGet));
		$model->setAttribute("UrlQry",    $vQry);
		
		$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
		$model->setAttribute("CreatedBy",   Yii::app()->user->id);
		$model->save();
	}


	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		//fmt
		$criteria     = new CDbCriteria;

		//channel-name
		$byUserName   = trim(Yii::app()->request->getParam('byUserName'));
		if(strlen($byUserName))
		{
			$criteria->with = array(
				'byUsers' => array('joinType'=>'LEFT JOIN'),

			);
			$criteria->addCondition(" byUsers.Username LIKE '%".addslashes($byUserName)."%' ");
		}
		
		//addr
		$byModule  = trim(Yii::app()->request->getParam('byModule'));
		if(strlen($byModule))
		{
			$criteria->addCondition(" UPPER(t.ModPage) LIKE '%".strtoupper(addslashes($byModule))."%' ");
		}
		
		//date
		$byDateFr  = trim(Yii::app()->request->getParam('byDateFr'));
		if(strlen($byDateFr))
		{
		   $criteria->addCondition(" t.LogDate >= '".addslashes($byDateFr)." 00:00:00' ");
		}		
		$byDateTo  = trim(Yii::app()->request->getParam('byDateTo'));
		if(strlen($byDateTo))
		{
		   $criteria->addCondition(" t.LogDate <= '".addslashes($byDateTo)." 23:59:59' ");
		}	
		
		
		//addr
		$byUserType  = trim(Yii::app()->request->getParam('byUserType'));
		if(strlen($byUserType))
		{
			$criteria->addCondition(" UPPER(t.UserType) LIKE '".strtoupper(addslashes($byUserType))."' ");
		}

		//client
		$byClientId  = trim(Yii::app()->request->getParam('byClientId'));
		if(strlen($byClientId))
		{
			$t    = addslashes($byClientId);
			$criteria->addCondition(" ( t.ClientId = '$t' )");
		}

		
		
		//clientlist
		$_clientlists = Clients::model()->findAll(array(
				          'select'=>'ClientId, CompanyName'));
		$clientlists  = array();
		foreach($_clientlists as $row) {
			$clientlists["$row->ClientId"] = $row->CompanyName;
		}
		
		
		//userlist
		$_usernames = Users::model()->findAll(array(
				     'select'=>'UserId, Username'));
		$usernames = array();
		foreach($_usernames as $row) {
			$usernames["$row->Username"] = $row->Username;

		}
		// set sort options
		$sort = new CSort;
		$sort->defaultOrder = ' t.LogDate DESC ';
		//get it
		$dataProvider = new CActiveDataProvider('AuditLogs', array(
			'criteria'=> $criteria ,
			'sort'    => $sort
		));		
	
		//get csv
		$csv = $this->formatCsv($criteria);
	
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'usertypelist'    => Yii::app()->params['UserTypes'],
			'usermodulelist'  => Yii::app()->params['Pages'],
			'usernamelist'    => $usernames,
			'clientlists'     => $clientlists,
			'downloadCSV'     => (@intval($csv['total'])>0)?($csv['fn']):(''),
		));
	}

	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Campaigns the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model= AuditLogs::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Campaigns $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='auditlogs-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	protected function formatCsv($criteria)
	{
		$fn   = sprintf("%s-%s-%s-%s.csv",'Audit-Logs',@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//ensure
		if (!@file_exists(Yii::app()->params['reportCsv'])) {
		    @mkdir(Yii::app()->params['reportCsv'], 0777, true);
		}
		
		//get it
		$csvs = new CActiveDataProvider('AuditLogs', array(
			'criteria'=>$criteria,
		));
		
		//set
		$csvs->setPagination(false);
		$total = 0;
		


		//hdr
		$hdr = sprintf('="ID",="USER NAME",="USER TYPE",="MODULE",="ACTION",="IP",="CLIENT",="DATE",="TIME",="AGENT",="",');
		
		$this->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		
		//get csv
		foreach($csvs->getData() as $row) 
		{
		    $total++;
		    
		    //hdr
		    $str = sprintf('="%s",="%s",="%s",="%s",="%s",="%s",="%s",="%s",="%s",="%s",="",',
					$row->AuditId,
					(($row->byUsers!=null)?($row->byUsers->Username):("")),
					$row->UserType,
					($row->ModPage   != null)?($row->ModPage)  :(""),
					($row->ModAction != null)?($row->ModAction):(""),
					$row->IPAddr,
					($row->byClients != null)?($row->byClients->CompanyName):(""),
					($row->LogDate   != null)?(substr($row->LogDate,0,10)):(""),
					($row->LogDate   != null)?(substr($row->LogDate,11)  ):(""),
					@str_replace(',',' ',$row->UserAgent)
					);
		    $this->io_save($csv, str_replace("\n",'', $str)."\n",'a');

		}
		
		//give it back
		return array(
			'total' => $total,
			'fn'    => $fn
		);
	}
	
	protected function io_save($fname='', $body='', $mode = 'w')
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
	
	public function actionCsv()
	{
		$fn   = trim(Yii::app()->request->getParam('fn'));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
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
	
}
