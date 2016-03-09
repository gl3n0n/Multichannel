<?php

class CustomerSubscriptionsController extends Controller
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
				'actions'=>array('create','update'),
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
		$model=new CustomerSubscriptions;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['CustomerSubscriptions']))
		{
			$model->attributes=$_POST['CustomerSubscriptions'];
			if($model->save()){
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->SubscriptionId));
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

		if(isset($_POST['CustomerSubscriptions']))
		{
			$model->attributes=$_POST['CustomerSubscriptions'];
			if($model->save()){
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->SubscriptionId));
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
	public function actionIndex($customer_id='')
	{
		$criteria    = new CDbCriteria;
		$customer_id = trim(Yii::app()->getRequest()->getParam('customer_id'));
		

		//points
		$byPointsName   = trim(Yii::app()->request->getParam('byPointsName'));
		$ofilter        = '';
		if(strlen($byPointsName))
		{
		    $t = addslashes($byPointsName);
			$ofilter = " AND pts.Name LIKE '%$t%' ";
		}	
		//byBrandName
		$byBrandName   = trim(Yii::app()->request->getParam('byBrandName'));
		$pfilter       = '';
		if(strlen($byBrandName))
		{
		    $t = addslashes($byBrandName);
			$pfilter = " AND brnd.BrandName LIKE '%$t%' ";
		}	
		//byChannelName
		$byChannelName = trim(Yii::app()->request->getParam('byChannelName'));
		$qfilter       = '';
		if(strlen($byChannelName))
		{
		    $t = addslashes($byChannelName);
			$qfilter = " AND EXISTS (
							SELECT 1 
							FROM
							channels cn
							WHERE
							1=1
							AND cn.ClientId   = cs.ClientId
							AND cn.BrandId    = cs.BrandId
							AND cn.CampaignId = cs.CampaignId
							AND cn.ChannelName LIKE '%$t%'
						)
						";
		}			
		
		//by client
		$rfilter       = '';
		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN' and isset($_REQUEST['Clients'])) 
		{
			$byClient = $_REQUEST['Clients']['ClientId'];
			if($byClient>0)
			{
				$t = addslashes($byClient);
				$rfilter       = " AND cs.ClientId = '$t' ";
			}			
		}
		//client 
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') 
		{
			$t = addslashes(Yii::app()->user->ClientId);
			$rfilter       = " AND cs.ClientId = '$t' ";
		}

		//customer
		$sfilter = '';
		if ($customer_id != '')
		{
			$t        = addslashes($customer_id);
			$sfilter  = " AND cs.CustomerId = '$t' ";	
		}
		
		if(1){
		$rawSql   = "
				SELECT 
					cs.*,
					brnd.BrandName,
					camp.CampaignName,
					(
						select y.ChannelName
						from
						channels y
						where
						1=1
						AND  y.ClientId   = cs.ClientId
						AND  y.BrandId    = cs.BrandId
						AND  y.CampaignId = cs.CampaignId
						limit 1 
					) as ChannelName ,
					clnt.CompanyName as ClientName,
					pts.Name as PointsSystemName,
					CONCAT(cust.LastName, ' ',cust.FirstName ) as CustomerName
				FROM 
					  customer_subscriptions cs,
					  customers cust,
					  clients clnt,
					  points pts,
					  brands brnd,
					  campaigns camp
				WHERE 1=1
					  AND   cs.CustomerId     = cust.CustomerId
					  AND   cs.ClientId       = clnt.ClientId
					  AND   cs.PointsId       = pts.PointsId
					  AND   cs.BrandId        = brnd.BrandId
					  AND   cs.CampaignId     = camp.CampaignId
						$ofilter 
						$pfilter 
						$qfilter 
						$rfilter 
						$sfilter 
			ORDER BY cs.DateCreated DESC
			";

			//echo "rawSql:$rawSql";exit();
			$sort = new CSort;
			$sort->attributes = array('*');
			//run
			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'SubscriptionId',
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
			);

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
		$model=new CustomerSubscriptions('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['CustomerSubscriptions']))
			$model->attributes=$_GET['CustomerSubscriptions'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return CustomerSubscriptions the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=CustomerSubscriptions::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CustomerSubscriptions $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='customer-subscriptions-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
