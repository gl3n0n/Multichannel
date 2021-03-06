<?php

class PointsSystemController extends Controller
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
			// array('allow',  // allow all users to perform 'index' and 'view' actions
			// 	'actions'=>array('index','view'),
			// 	'users'=>array('*'),
			// ),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','view','create','update','list','delete'),
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


	protected function getClientList()
	{
		// Uncomment the following line if AJAX validation is needed
		$xmore = '';
		if(Yii::app()->user->AccessType !== "SUPERADMIN") {
			$xmore = " AND ClientId = '".addslashes(Yii::app()->user->ClientId)."' ";
		}
		$_list = Clients::model()->findAll(array(
			  'select'=>'ClientId, CompanyName', 'condition' => " status='ACTIVE' $xmore "));
		$data = array();
		foreach($_list as $row) {
			$data[$row->ClientId] = $row->CompanyName;

		}
		//give it back
		return $data;
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		
		$model = new PointsSystem;

		if(isset($_POST['PointsSystem']))
		{
			$model->attributes=$_POST['PointsSystem'];

			//reset the campaignId
			$model->setAttribute("Status", 'ACTIVE');
			if(Yii::app()->user->AccessType !== "SUPERADMIN") {
				$model->setAttribute("ClientId",    Yii::app()->user->ClientId);
			}			
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy",   Yii::app()->user->id);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy",   Yii::app()->user->id);
			
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->PointsId));
			}
			else
			{
				Yii::app()->user->setFlash('error', 'An unexpected error occured.');
			}
			
		
		}
		

		
		$this->render('create',array(
			'model'      => $model,
			'client_list' => $this->getClientList(),
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		
		//NOTE: this will need to be modified to prevent users from other clients from viewing others' records
		$model=$this->loadModel($id);

		$old_attrs = @var_export($model->attributes,1);
		
		if(isset($_POST['PointsSystem']))
		{
			$model->attributes=$_POST['PointsSystem'];
			
			$new_attrs = @var_export($model->attributes,1);
			$audit_logs= sprintf("OLD:\n\n%s\n\nNEW:\n\n%s",$old_attrs,$new_attrs);
			
			//reset the campaignId
			// $model->setAttribute("Status", 'ACTIVE');
			if(Yii::app()->user->AccessType !== "SUPERADMIN"){
				$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			}

			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);

			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs(null,$audit_logs);
				$this->redirect(array('view','id'=>$model->PointsId));
			}
		}

		$this->render('update',array(
			'model'      => $model,
			'client_list' => $this->getClientList(),
		));


	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		
		$model    = $this->loadModel($id);
		$rowCount = $model->findByPk($id)->delete();
		
		$utilLog = new Utils;
		$utilLog->saveAuditLogs();
	
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{

		$criteria = new CDbCriteria;
		
		//name
		$byName   = trim(Yii::app()->request->getParam('byName'));
		if(strlen($byName))
		{
			$criteria->addCondition(" (
			 	t.Name     LIKE '%".addslashes($byName)."%' 
			 ) ");
		}			
		//status
		$byStatusType = trim(Yii::app()->request->getParam('byStatusType'));
		if(strlen($byStatusType))
		{
			$t = addslashes($byStatusType);
			$criteria->addCondition(" (  t.Status = '$t' )  ");
		}			
		//by client
		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN' and isset($_REQUEST['Clients'])) 
		{
			$byClient = $_REQUEST['Clients']['ClientId'];
			if($byClient>0)
			{
				$t = addslashes($byClient);
				$criteria->addCondition(" (  t.ClientId = '$t' )  ");
			}			
		}
		
		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN') {
			$dataProvider = new CActiveDataProvider('PointsSystem', array(
				'criteria'=>$criteria ,
				'sort'    => array(
							'defaultOrder' => ' t.PointsId DESC ',
							)			
				
			));
		} else {
			$criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
			$dataProvider = new CActiveDataProvider('PointsSystem', array(
				'criteria'=>$criteria ,
				'sort'    => array(
							'defaultOrder' => ' t.PointsId DESC ',
							)				
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
		$model=new PointsSystem('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PointsSystem']))
			$model->attributes=$_GET['PointsSystem'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return PointsSystem the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=PointsSystem::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param PointsSystem $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='pointssystem-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
