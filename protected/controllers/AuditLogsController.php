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
				'actions'=>array('index','view','create','update','list','admin','delete'),
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
		
		//get it
		$dataProvider = new CActiveDataProvider('AuditLogs', array(
			'criteria'=>$criteria ,
		));		
							
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
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
}
