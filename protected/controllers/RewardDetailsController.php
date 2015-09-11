<?php

class RewardDetailsController extends Controller
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','getPointSystemlist','getRewardslist'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
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
		$model=new RewardDetails;
		
		$clientsID = Users::model()->findByPk(Yii::app()->user->id)->ClientId;
		
		if(Yii::app()->user->AccessType === "SUPERADMIN" && $model->scenario === 'insert') {
			$_clients = Clients::model()->active()->findAll();
		} else {
			$_clients = Clients::model()->findAll(array(
				'select'=>'ClientId, CompanyName', 'condition'=>'ClientId='.$clientsID.''));
		}
		
		$clients = array();
		foreach($_clients as $row) {
			$clients[$row->ClientId] = $row->CompanyName;

		}

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['RewardDetails']))
		{
			$model->attributes=$_POST['RewardDetails'];
			
			//reset the campaignId
			$model->setAttribute("Status", 'ACTIVE');
			if(Yii::app()->user->AccessType !== "SUPERADMIN") {
				$model->setAttribute("ClientId",    Yii::app()->user->ClientId);
			}		
			//get points id
			$RewardId = '';
			$ClientIda= '';
			$PointsId = '';
			$ClientId = '';
			if(!empty($_POST['RewardDetails']['RewardId']))
			list($ClientIda, $RewardId)  = @explode('-',trim($_POST['RewardDetails']['RewardId']));
			if(!empty($_POST['RewardDetails']['PointsId']))
			list($PointsId, $ClientId  ) = @explode('-',trim($_POST['RewardDetails']['PointsId']));
			
			//save it
			$model->setAttribute("RewardId",    $RewardId);
			$model->setAttribute("PointsId",    $PointsId);
			$model->setAttribute("ClientId",    $ClientId);
			$model->setAttribute("StartDate",   substr($_POST['RewardDetails']['StartDate'],0,10));
			$model->setAttribute("EndDate",     substr($_POST['RewardDetails']['EndDate']  ,0,10));
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy",   Yii::app()->user->id);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy",   Yii::app()->user->id);
			
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->RewardConfigId));
			}
			else
			{
				Yii::app()->user->setFlash('error', 'An unexpected error occured.');
			}
		}
		
		$get_active_clause='Status=\'ACTIVE\'';
		/*
		$_clients = Clients::model()->active()->findAll(array('select'=>'ClientId, CompanyName'));
		$clients = CHtml::listData($_clients, 'ClientId', 'CompanyName');
		*/
		
		$_rewardslistCriteria = new CDbCriteria;
		$_rewardslist = RewardsList::model()->thisClient()->active()->findAll(array('select'=>'RewardId, Title, ClientId'));
		if(Yii::app()->user->AccessType === "SUPERADMIN")
		$_rewardslist = RewardsList::model()->active()->findAll(array('select'=>'RewardId, Title, ClientId'));
		
		$rewardslist  = array();
		foreach($_rewardslist as $row) {
			$vkey                 = sprintf("%s-%s",$row->RewardId ,$row->ClientId );
			$rewardslist["$vkey"] = $row->Title;
		}
		
		
		$_pointsSystemCriteria = new CDbCriteria;
		$_pointsSystem = PointsSystem::model()->thisClient()->active()->findAll(array('select'=>'PointsId, Name'));
		if(Yii::app()->user->AccessType === "SUPERADMIN")
		$_pointsSystem = PointsSystem::model()->active()->findAll(array('select'=>'PointsId, Name'));
		
		
		$pointslist = CHtml::listData($_pointsSystem, 'PointsId', 'Name');
		
		if(0)
		{
			echo 'HEHEHE<hr>'.@var_export($_rewardslist,true);
			echo 'HEHEHE<hr>'.@var_export($rewardslist,true);
			exit;
		}

		$this->render('create',array(
			'model'        => $model,
			'client_list'  => $clients,
			'points_id'    => $this->getDropList(),
			'rewardlist_id'=> $rewardslist,
		));
	}


	protected function getDropList()
	{
	
		$criteria = new CDbCriteria;
		// Uncomment the following line if AJAX validation is needed
		$xmore = '';
		if(Yii::app()->user->AccessType !== "SUPERADMIN") {
			$xmore = " AND t.ClientId = '".addslashes(Yii::app()->user->ClientId)."' ";
		}
		$criteria->addCondition(" t.status='ACTIVE' $xmore ");
		$_list = PointsSystem::model()->with('byClients')->findAll($criteria);
		$data  = array();
		foreach($_list as $row) {
			$vkey = sprintf("%s-%s",$row->PointsId ,$row->ClientId );
			$data["$vkey"] = sprintf("%s ( %s )",$row->Name,($row->byClients!=null ? ($row->byClients->CompanyName) : ("")));

		}
		//give it back
		return $data;
	}
	
	public function actionGetRewardslist()
	{
		//give
		$criteria = new CDbCriteria;
		
		//get params
		// list($ClientId, $RewardId ) = @explode('-',trim(Yii::app()->request->getParam('ClientId')));
		$ClientId = Yii::app()->request->getParam('ClientId');
		
		// Uncomment the following line if AJAX validation is needed
		$xmore = " AND t.ClientId = '".addslashes($ClientId)."' ";
		$criteria->addCondition(" t.status='ACTIVE' $xmore ");
		$_list = RewardsList::model()->with('rewardClients')->findAll($criteria);
		$data  = array();
		foreach($_list as $row) {
			$vkey = sprintf("%s-%s",$row->ClientId, $row->RewardId );
			$data["$vkey"] = sprintf("%s ( %s )",$row->Title,($row->rewardClients!=null ? ($row->rewardClients->CompanyName) : ("")));

		}
		//give it back
		Yii::app()->utils->sendJSONResponse($data);
	}
	
	public function actionGetPointSystemlist()
	{
		//give
		$criteria = new CDbCriteria;
		
		//get params
		//list($RewardId, $ClientId ) = @explode('-',trim(Yii::app()->request->getParam('RewardId')));
		list($ClientId, $RewardId) = @explode('-',trim(Yii::app()->request->getParam('RewardId')));
		
		// Uncomment the following line if AJAX validation is needed
		$xmore = " AND t.ClientId = '".addslashes($ClientId)."' ";
		$criteria->addCondition(" t.status='ACTIVE' $xmore ");
		$_list = PointsSystem::model()->with('byClients')->findAll($criteria);
		$data  = array();
		foreach($_list as $row) {
			$vkey = sprintf("%s-%s",$row->PointsId ,$row->ClientId );
			$data["$vkey"] = sprintf("%s ( %s )",$row->Name,($row->byClients!=null ? ($row->byClients->CompanyName) : ("")));

		}
		//give it back
		Yii::app()->utils->sendJSONResponse($data);
	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['RewardDetails']))
		{
			$model->attributes=$_POST['RewardDetails'];
			
			
			//get points id
	
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->RewardConfigId));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		/*
		$dataProvider=new CActiveDataProvider('RewardDetails');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
		*/
		
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		if(strlen($search))
		{
			$criteria->addCondition(" (
			 	t.Name     LIKE '%".addslashes($search)."%' 
			 ) ");
		}			

		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN') {
			$dataProvider = new CActiveDataProvider('RewardDetails', array(
				'criteria'=>$criteria ,
			));
		} else {
			$criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
			$dataProvider = new CActiveDataProvider('RewardDetails', array(
				'criteria'=>$criteria ,
			));
		}

		//get models
		$this->render('index',array(
			'dataProvider'=> $dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new RewardDetails('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['RewardDetails']))
			$model->attributes=$_GET['RewardDetails'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return RewardDetails the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=RewardDetails::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param RewardDetails $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='reward-details-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
