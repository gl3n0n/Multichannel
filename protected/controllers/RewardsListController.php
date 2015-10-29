<?php

class RewardsListController extends Controller
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','index','view'),
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
		$model=new RewardsList;

		
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

		if(isset($_POST['RewardsList']))
		{
			$model->attributes=$_POST['RewardsList'];
			//client-ids
			$cid = Yii::app()->user->ClientId;
			$model->setAttribute("ClientId",$cid);
			if(Yii::app()->user->AccessType === "SUPERADMIN" && $model->scenario === 'insert') 
			{
				foreach($_clients as $row) 
				{
					if($row->ClientId == $_POST['RewardsList']['ClientId'])
					{
						$cid = $row->ClientId;
						$model->setAttribute("ClientId",$cid);
						break;
					}
				}
			}

			// We get the image information using CUploadedFile class.
			// The model object and attribute name is passed when the getInstance method is called.
			$UploadFile = CUploadedFile::getInstance($model,'Image');
			Yii::app()->user->setFlash('notice', var_export($UploadFile));
			if ($UploadFile !== null) {
				$imageFilename = mktime() . '_' . $UploadFile->name;
				// $model->File = $couponFilename;
				$model->Image = Yii::app()->params['baseUploadUrl'] . 'rewards_list/' . $imageFilename;

			}
			else Yii::app()->user->setFlash('notice', 'Error: File is null.');
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("Status", 'ACTIVE');
			$model->setAttribute("CreatedBy", Yii::app()->user->id);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save())
			{

				$utilLog = new Utils;
				$utilLog->saveAuditLogs();

				try {
					if($UploadFile !== null) {
						$UploadFile->saveAs(Yii::app()->params['uploadImageDir'] . 'rewards_list/'  . $imageFilename);
					}
				} catch (Exception $ex) {
					$model->addError('File', 'Failed to upload file.');
					Yii::app()->user->setFlash('error', 'Error: ' . $ex->getMessage());
				}
				$this->redirect(array('view','id'=>$model->RewardId));
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'client_list'  => $clients,
		));
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

		if(isset($_POST['RewardsList']))
		{
			$old_attrs = @var_export($model->attributes,1);
			
			$model->attributes=$_POST['RewardsList'];
			

			$new_attrs = @var_export($model->attributes,1);
			$audit_logs= sprintf("OLD:\n\n%s\n\nNEW:\n\n%s",$old_attrs,$new_attrs);

			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save()){
				$utilLog = new Utils;
				$utilLog->saveAuditLogs(null,$audit_logs);

				$this->redirect(array('view','id'=>$model->RewardId));
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
		$utilLog = new Utils;
		$utilLog->saveAuditLogs();
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$criteria = new CDbCriteria;
		
		//bytitle
		$byName   = trim(Yii::app()->request->getParam('byName'));
		if(strlen($byName))
		{
			$t = addslashes($byName);
			$criteria->addCondition(" (t.Title LIKE '%$t%' OR t.Description LIKE '%$t%' )  ");
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
		//date: 
		$byTranDateFr = trim(Yii::app()->request->getParam('byTranDateFr'));
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateFr))
		{
			$t = addslashes($byTranDateFr);
			$criteria->addCondition(" ( t.Availability >= '$t 00:00:00' ) ");
		}
		//date: 
		$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
		{
			$t = addslashes($byTranDateTo);
			$criteria->addCondition(" ( t.Availability <= '$t 23:59:59' ) ");
		}		

		
		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN') {
			$dataProvider = new CActiveDataProvider('RewardsList', array(
							'criteria'=>$criteria ,
			));
		} else {
			if(0){
			$dataProvider = new CActiveDataProvider('RewardsList', array(
				'criteria'=>array(
				    'scopes'=>array('thisClient'),
				),
			));
			}
			$criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
			$dataProvider = new CActiveDataProvider('RewardsList', array(
							'criteria'=>$criteria ,
			));
			
		}

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new RewardsList('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['RewardsList']))
			$model->attributes=$_GET['RewardsList'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return RewardsList the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=RewardsList::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param RewardsList $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='rewards-list-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
