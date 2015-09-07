<?php

class PointsToCouponController extends Controller
{
	public $extraJS;
	public $mainDivClass;
	public $modals;
	public $statusMsg;
	
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
				'actions'=>array('index','view','create','update','list'),
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
		$model=new PointsToCoupon;


		if(isset($_POST['PointsToCoupon']))
		{
			$model->attributes=$_POST['PointsToCoupon'];
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy", Yii::app()->user->id);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			$model->setAttribute("ClientId",  Yii::app()->user->ClientId);
			$model->setAttribute("Status", 'ACTIVE');
			if($model->save())
				$this->redirect(array('view','id'=>$model->PtcId));
		}

		$this->render('create',array(
			'model'       =>$model,
			'coupon_list' =>$this->getCouponList(),
			
		));
	}

	protected function getCouponList()
	{
	
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$criteria = new CDbCriteria;
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')   
		{
			$criteria->with = array(
				'couponMap' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" ( couponMap.ClientId = '".addslashes(Yii::app()->user->ClientId)."' ) ");
		}


		$_coupon = Coupon::model()->findAll($criteria);
		$coupons = array();
		foreach($_coupon as $row) {
			$coupons[$row->CouponId] = $row->CouponId . ' - ' - $row->Code;

		}
		//give
		return $coupons;
	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['PointsToCoupon']))
		{
			$model->attributes=$_POST['PointsToCoupon'];
			$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			$model->setAttribute("Status", 'ACTIVE');
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);

			if($model->save())
				$this->redirect(array('view','id'=>$model->PtcId));
		}

		$this->render('update',array(
			'model'       =>$model,
			'coupon_list' =>$this->getCouponList(),
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
		if(strlen($search))
		{
			$criteria->addCondition(" (
			 	t.Title     LIKE '%".addslashes($search)."%' 
			 ) ");
		}
		

		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') 
		{
			 $criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
		}

		
		$dataProvider = new CActiveDataProvider('PointsToCoupon', array(
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
		$model=new PointsToCoupon('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PointsToCoupon']))
			$model->attributes=$_GET['PointsToCoupon'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return PointsToCoupon the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=PointsToCoupon::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param PointsToCoupon $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='raffle-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	
}
