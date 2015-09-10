<?php

class CouponToPointsController extends Controller
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
		$model=new CouponToPoints;


		if(isset($_POST['CouponToPoints']))
		{
			$model->attributes=$_POST['CouponToPoints'];
			//$row->CouponId ,$row->ClientId 
			list($CouponId, $ClientId) = @explode('-',$_POST['CouponToPoints']['CouponId']);
			$model->setAttribute("ClientId",$ClientId);
			$model->setAttribute("CouponId",$CouponId);
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy",   Yii::app()->user->id);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy",   Yii::app()->user->id);
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();

				$this->redirect(array('view','id'=>$model->CtpId));
			}
		}

		$this->render('create',array(
			'model'       =>$model,
			'coupon_list' =>$this->getDropList(),
			
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

		if(isset($_POST['CouponToPoints']))
		{
			$model->attributes=$_POST['CouponToPoints'];
			list($CouponId, $ClientId) = @explode('-',$_POST['CouponToPoints']['CouponId']);
			$model->setAttribute("ClientId",$ClientId);
			$model->setAttribute("CouponId",$CouponId);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy",   Yii::app()->user->id);

			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->CtpId));
			}
		}

		$this->render('update',array(
			'model'       =>$model,
			'coupon_list' =>$this->getDropList(),
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
			 	t.Name LIKE '%".addslashes($search)."%' 
			 ) ");
		}
		
		
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') 
		{
			 $criteria->compare('t.ClientId', Yii::app()->user->ClientId, true); 
		}
		

		$dataProvider = new CActiveDataProvider('CouponToPoints', array(
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
		$model=new CouponToPoints('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['CouponToPoints']))
			$model->attributes=$_GET['CouponToPoints'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return CouponToPoints the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=CouponToPoints::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CouponToPoints $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='raffle-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
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
		$_list = CouponSystem::model()->with('byClients')->findAll($criteria);
		$data  = array();
		foreach($_list as $row) {
			$vkey = sprintf("%s-%s",$row->CouponId ,$row->ClientId );
			$data["$vkey"] = sprintf("%s ( %s )",$row->CouponName,($row->byClients!=null ? ($row->byClients->CompanyName) : ("")));

		}
		//give it back
		return $data;
	}
		
}
