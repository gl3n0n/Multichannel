<?php

class CampaignsController extends Controller
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
				'actions'=>array('index','view','create','update','list','test'),
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

	public function actionTest()
	{
		$model = Campaigns::model()->findAll();
		echo "<pre>";
		foreach($model as $row) { var_dump($row); echo "<br><br>\n"; }
	}

	public function actionList()
	{
		// $CampaignsModel = Campaigns::model()->with('campaignBrands')->findAll();

		// echo "Count: " . count($CampaignsModel); exit;
		// var_dump(count($CampaignsModel)); exit;

		// $BrandsModel = Brands::model()->thisClient()->findAll(array('select'=>'BrandId'));
		// $brandIds    = array();

		// foreach($BrandsModel as $row) { $brandIds[] = $row->BrandId; }

		// if(empty($brandIds)) Yii::app()->utils->sendJSONResponse(array('data'=>$brandIds));

		// $criteria = new CDbCriteria;
		// $criteria->addInCondition('BrandId', $brandIds);
		
		// $data = Campaigns::model()->findAll($criteria);

		// foreach($data as $row) { var_export($row->attributes); echo "<br><br>\n"; }
		// Yii::app()->end();
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Campaigns;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		$_brands = Brands::model()->thisClient()->findAll(array(
			'select'=>'BrandId, BrandName', 'condition'=>'status=\'ACTIVE\''));
		$brands = array();
		foreach($_brands as $row) {
			$brands[$row->BrandId] = $row->BrandName;

		}

		if(isset($_POST['Campaigns']))
		{
			$model->attributes=$_POST['Campaigns'];
			if(Yii::app()->user->AccessType !== "SUPERADMIN" && $model->scenario === 'insert') {
				$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			}
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy", Yii::app()->user->id);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();

				$this->redirect(array('view','id'=>$model->CampaignId));
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'brand_list'=>$brands,
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
		//$_brands = Brands::model()->findAll(array(
		$_brands = Brands::model()->thisClient()->findAll(array(			
			'select'=>'BrandId, BrandName', 'condition'=>'status=\'ACTIVE\''));
		$brands = array();
		foreach($_brands as $row) {
			$brands[$row->BrandId] = $row->BrandName;

		}

		if(isset($_POST['Campaigns']))
		{
			$old_attrs = @var_export($model->attributes,1);
			
			$model->attributes=$_POST['Campaigns'];
			
			$new_attrs = @var_export($model->attributes,1);
			$audit_logs= sprintf("OLD:\n\n%s\n\nNEW:\n\n%s",$old_attrs,$new_attrs);
			
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs(null,$audit_logs);

				$this->redirect(array('view','id'=>$model->CampaignId));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'brand_list'=>$brands,
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

		//name
		$byName   = trim(Yii::app()->request->getParam('byName'));
		if(strlen($byName))
		{
		    $t = addslashes($byName);
			$criteria->addCondition(" ( t.CampaignName     LIKE '%$t%' ) ");
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
			$criteria->addCondition(" ( t.DurationFrom >= '$t 00:00:00' ) ");
		}
		//date: 
		$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
		{
			$t = addslashes($byTranDateTo);
			$criteria->addCondition(" ( t.DurationTo <= '$t 23:59:59' ) ");
		}		

		

		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN') {
			$dataProvider = new CActiveDataProvider('Campaigns', array(
							'criteria'     => $criteria ,
							'sort'         => array(
											'defaultOrder' => ' t.CampaignId DESC ',
											)
			));
		} else {
			if(0){
			$dataProvider = new CActiveDataProvider('Campaigns', array(
				'criteria'=>array(
				    'scopes'=>array('thisClient'),
				),
			));
			}
			$criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
			$dataProvider = new CActiveDataProvider('Campaigns', array(
							'criteria' => $criteria ,
							'sort'     => array(
										'defaultOrder' => ' t.CampaignId DESC ',
										)
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
		$model=new Campaigns('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Campaigns']))
			$model->attributes=$_GET['Campaigns'];

		$this->render('admin',array(
			'model'=>$model,
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
		$model=Campaigns::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='campaigns-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
