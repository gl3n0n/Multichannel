<?php

class CustomersController extends Controller
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
				'actions'=>array('update'),
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
		$model = $this->loadModel($id);
		$total = $this->getSummaryPts(($model != null?$model->CustomerId:0));
		$this->render('view',array(
			'model'=> $model,
			'total'=> @intval($total),
		));
	}
	
	protected function getSummaryPts($custId=0)
	{
		$rawCount = 0;
		if(1){
		$rawSql   = "
			select sum(Points) from (
			select a.CustomerId, a.SubscriptionId, a.ClientId, a.BrandId, a.CampaignId, a.status SubsriptionStatus,
			       b.Balance, b.Used, b.Total,
			       c.PointsId, e.Value Points
			from  customer_subscriptions a, customer_points b, points_log c, points d, action_type e
			where a.CustomerId = '$custId'
			and   a.SubscriptionId = b.SubscriptionId
			and   a.SubscriptionId = c.SubscriptionId
			and   a.CustomerId = c.CustomerId
			and   c.PointsId = d.PointsId
			and   c.ActiontypeId = e.ActiontypeId
			union all
			select a.CustomerId, a.SubscriptionId, a.ClientId, a.BrandId, a.CampaignId, a.status SubsriptionStatus,
			       b.Balance, b.Used, b.Total,
			       ifnull(c.PointsId,0), c.Value Points
			from  customer_subscriptions a, customer_points b, points_log c
			where a.CustomerId = '$custId'
			and   a.SubscriptionId = b.SubscriptionId
			and   a.SubscriptionId = c.SubscriptionId
			and   a.CustomerId = c.CustomerId
			and   (c.PointsId = 0 or c.PointsId is null)
			) as count_alias
		";
		$rawCount = Yii::app()->db->createCommand(" $rawSql ")->queryScalar(); //the count
		
		}

		//give it back
		return $rawCount;
	
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Customers;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Customers']))
		{
			$model->attributes=$_POST['Customers'];
			
			if(Yii::app()->user->AccessType !== "SUPERADMIN" ) {
				$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			}
			
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->CustomerId));
			}
		}

		$this->render('create',array(
			'model'=>$model,
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

		if(isset($_POST['Customers']))
		{
			$model->attributes=$_POST['Customers'];
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->CustomerId));
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
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		if($search) 
			$criteria->compare('FirstName', $search, true);


		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') 
		{
			 $criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
		}

		$dataProvider = new CActiveDataProvider('Customers', array(
			'criteria'=>$criteria ,
			));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Customers('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Customers']))
			$model->attributes=$_GET['Customers'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Customers the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Customers::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Customers $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='customers-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
