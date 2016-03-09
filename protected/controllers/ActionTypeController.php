<?php

class ActionTypeController extends Controller
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


	protected function getPointsList()
	{
		// Uncomment the following line if AJAX validation is needed
		$xmore = '';
		if(Yii::app()->user->AccessType !== "SUPERADMIN") {
			$xmore = " AND ClientId = '".addslashes(Yii::app()->user->ClientId)."' ";
		}
		$_points = PointsSystem::model()->findAll(array(
			  'select'=>'PointsId, Name', 'condition' => " status='ACTIVE' $xmore "));
		$points = array();
		foreach($_points as $row) {
			$points[$row->PointsId] = $row->Name;

		}
		//give it back
		return $points;
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		
		$model = new ActionType;

		if(isset($_POST['ActionType']))
		{
			$model->attributes=$_POST['ActionType'];
			
			//default
			$ClientID = trim(Yii::app()->user->ClientId);
			$xmore    = '';
			if(Yii::app()->user->AccessType == "SUPERADMIN") 
			{
				$_points = PointsSystem::model()->findAll(array(
							'select'=>'PointsId, Name, ClientId', 'condition' => " status='ACTIVE' $xmore "));
				$points = array();
				foreach($_points as $row) {
					if($_POST['ActionType']['PointsId'] == $row->PointsId)
					{
						$ClientID = trim($row->ClientId);
						break;
					}
				}
			}
			
			// check if value/multiplier is greater than Points Limit
			// MULTIPLIER = ActionType,Value
			// LIMIT      = ActionType,PointsLimit
			if (@intval($_POST['ActionType']['PointsLimit']) != 0)
			{
				if ($_POST['ActionType']['PointsLimit'] < $_POST['ActionType']['Value'])
				{
					//reset the campaignId
					$model->setAttribute("Status", 'ACTIVE');
					$model->setAttribute("ClientId", $ClientID);
					$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
					$model->setAttribute("CreatedBy", Yii::app()->user->id);
					$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
					$model->setAttribute("UpdatedBy", Yii::app()->user->id);
				}
				else
				{
					$model->addError('Value', 'Multiplier/Value must be greater than the Points Limit.');
				}

			}
			else
			{
				//reset the campaignId
				$model->setAttribute("Status", 'ACTIVE');
				$model->setAttribute("ClientId", $ClientID);
				$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
				$model->setAttribute("CreatedBy", Yii::app()->user->id);
				$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
				$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			}
			
			if(!$model->hasErrors())
			{
				if($model->save())
				{
					$utilLog = new Utils;
					$utilLog->saveAuditLogs();
					$this->redirect(array('view','id'=>$model->ActiontypeId));
				}
				else
				{
					Yii::app()->user->setFlash('error', 'An unexpected error occured.');
				}

			}

			
		
		}
		

		
		$this->render('create',array(
			'model'      => $model,
			'pointslist' => $this->getPointsList(),
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


		if(isset($_POST['ActionType']))
		{
			$old_attrs = @var_export($model->attributes,1);
			
			$model->attributes=$_POST['ActionType'];

			$new_attrs = @var_export($model->attributes,1);
			$audit_logs= sprintf("OLD:\n\n%s\n\nNEW:\n\n%s",$old_attrs,$new_attrs);
			
			// check if value/multiplier is greater than Points Limit
			// if ($_POST['ActionType']['Value'] < $_POST['ActionType']['PointsLimit'])
			
			if ($_POST['ActionType']['PointsCapping'] == 'DAILY' && $_POST['ActionType']['PointsLimit'] == 0)
			{
				$model->addError('PointsCapping', 'You cannot set to 0 if point capping is daily');
			}
			else
			{
				if (@intval($_POST['ActionType']['PointsLimit']) != 0)
				{
					if ($_POST['ActionType']['PointsLimit'] > $_POST['ActionType']['Value'])
					{
						//echo 'dito?';
						//echo $_POST['ActionType']['PointsLimit'] .' < ' . $_POST['ActionType']['Value'];
						//exit();
						if(Yii::app()->user->AccessType !== "SUPERADMIN" && $model->scenario === 'insert') {
							$model->setAttribute("ClientId", Yii::app()->user->ClientId);
						}			
						
						//reset the campaignId
						$model->setAttribute("Status", 'ACTIVE');
						$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
						$model->setAttribute("UpdatedBy", Yii::app()->user->id);

					}
					else
					{
						$model->addError('Value', 'Multiplier/Value must be less than or equal to the Points Limit.');
					}
				}
				else
				{
					//reset the campaignId
					$model->setAttribute("Status", 'ACTIVE');
					$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
					$model->setAttribute("UpdatedBy", Yii::app()->user->id);
				}

			}
			
			if(!$model->hasErrors())
			{
				if($model->save())
				{
					$utilLog = new Utils;
					$utilLog->saveAuditLogs(null,$audit_logs);
					$this->redirect(array('view','id'=>$model->ActiontypeId));
				}

			}
			

		}

		$this->render('update',array(
			'model'      => $model,
			'pointslist' => $this->getPointsList(),
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
			$t = addslashes($byName);
			$criteria->addCondition(" (	t.Name LIKE '%$t%' ) ");
		}	

		//date: 
		$byTranDateFr = trim(Yii::app()->request->getParam('byTranDateFr'));
		$dtfilter1     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateFr))
		{
			$t = addslashes($byTranDateFr);
			$criteria->addCondition(" ( t.DateCreated >= '$t 00:00:00' ) ");
		}
		//date: 
		$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
		{
			$t = addslashes($byTranDateTo);
			$criteria->addCondition(" ( t.DateCreated <= '$t 23:59:59' ) ");
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
			$dataProvider = new CActiveDataProvider('ActionType', array(
				'criteria'=>$criteria ,
				'sort'    => array(
							'defaultOrder' => ' t.ActiontypeId DESC ',
							)
			));
		} else {
			$criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
			$dataProvider = new CActiveDataProvider('ActionType', array(
				'criteria'=>$criteria ,
				'sort'    => array(
							'defaultOrder' => ' t.ActiontypeId DESC ',
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
		$model=new ActionType('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ActionType']))
			$model->attributes=$_GET['ActionType'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ActionType the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=ActionType::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param ActionType $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='actiontype-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
