<?php

class BrandsController extends Controller
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

	public function actionList($ClientId=null)
	{
		$list = array();
		if(! $ClientId) Yii::app()->utils->sendJSONResponse($list);

		$search   = Yii::app()->request->getParam('search');
		$criteria = new CDbCriteria;
		if($search) $criteria->compare('BrandName', $search, true);
		
		$criteria->compare('ClientId', $ClientId, true);
		$criteria->compare('Status',   'active', true);

		//$model = Brands::model()->findAllByAttributes(array('ClientId'=>$ClientId, 'Status'=>'ACTIVE'));
		$model = Brands::model()->findAllByAttributes($criteria);

		foreach($model as $row) { $list[$row->BrandId] = $row->BrandName; }
		Yii::app()->utils->sendJSONResponse($list);
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
		$model=new Brands;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
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

		if(isset($_POST['Brands']))
		{
			$model->attributes=$_POST['Brands'];
			if(Yii::app()->user->AccessType !== "SUPERADMIN" && $model->scenario === 'insert') {
				$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			}
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy", Yii::app()->user->UserId);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save())
				$this->redirect(array('view','id'=>$model->BrandId));
		}

		$this->render('create',array(
			'model'=>$model,
			'client_list'=>$clients,
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

		// if(Users::model()->findByPk(Yii::app()->user->getId())->AccessType==='SUPERADMIN')
		// 	$model=$this->loadModel($id);
		// else
		// 	$model=Brands::model()->findByPk($id, array(
		// 		'scopes'=>array('thisClient'),
		// 	));

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		$_clients = Clients::model()->findAll(array(
			'select'=>'ClientId, CompanyName', 'condition'=>'status=\'ACTIVE\''));
		$clients = array();
		foreach($_clients as $row) {
			$clients[$row->ClientId] = $row->CompanyName;

		}

		if(isset($_POST['Brands']))
		{
			$model->attributes=$_POST['Brands'];
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save())
				$this->redirect(array('view','id'=>$model->BrandId));
		}

		$this->render('update',array(
			'model'=>$model,
			'client_list'=>$clients,
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

		$search   = Yii::app()->request->getParam('search');
		$criteria = new CDbCriteria;
		if($search) $criteria->compare('BrandName', $search, true);


		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN') {
			$dataProvider = new CActiveDataProvider('Brands', array(
				'criteria'=>$criteria ,
			));
		} else {
			$dataProvider = new CActiveDataProvider('Brands', array(
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
		$model=new Brands('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Brands']))
			$model->attributes=$_GET['Brands'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Brands the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Brands::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Brands $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='brands-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
