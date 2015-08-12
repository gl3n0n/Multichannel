<?php

class RaffleController extends Controller
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
				'actions'=>array('create','update','pending','approve'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','pending','approve'),
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
		$model=new Raffle;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		
		
		$_coupon = Coupon::model()->findAll();
		$coupons = array();
		foreach($_coupon as $row) {
			$coupons[$row->CouponId] = $row->CouponId;

		}

		if(isset($_POST['Raffle']))
		{
			$model->attributes=$_POST['Raffle'];
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy", Yii::app()->user->id);
			$model->setAttribute("DateUpdatted", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save())
				$this->redirect(array('view','id'=>$model->RaffleId));
		}

		$this->render('create',array(
			'model'=>$model,
			'coupon_id'=>$coupons
			
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
		

		$_coupon = Coupon::model()->findAll();
		$coupons = array();
		foreach($_coupon as $row) {
			$coupons[$row->CouponId] = $row->CouponId;

		}

		if(isset($_POST['Raffle']))
		{
			$model->attributes=$_POST['Raffle'];
			
			if($model->save())
				$this->redirect(array('view','id'=>$model->RaffleId));
		}

		$this->render('update',array(
			'model'=>$model,
			'coupon_id'=>$coupons
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
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		if($search) $criteria->compare('Source', $search, true);

		$dataProvider = new CActiveDataProvider('Raffle', array(
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
		$model=new Raffle('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Raffle']))
			$model->attributes=$_GET['Raffle'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}



	

	/**
	 * Manages all models.
	 */
	public function actionPending()
	{
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		if($search) $criteria->compare('Source', $search, true);

		//all-pending
		$criteria->addCondition("t.Status IN ('ACTIVE','PENDING') ");
		

		//brands		
		$_brands = Brands::model()->findAll(array(
				'select'=>'BrandId, BrandName', 'condition'=>" status='ACTIVE '"));
		$brands = CHtml::listData($_brands, 'BrandId', 'BrandName');

		//campaigns
		$_campaigns = Campaigns::model()->findAll(array(
			     'select'=>'CampaignId, CampaignName', 'condition'=>" status='ACTIVE '"));
		$campaigns  = CHtml::listData($_campaigns, 'CampaignId', 'CampaignName');

		//clients		
		$_clients   = Clients::model()->findAll(array(
				'select'=>'ClientId, CompanyName', 'condition'=>" status='ACTIVE '"));
		$clients    = CHtml::listData($_clients, 'ClientId',  'CompanyName');
		
		//channels
		$_channels   = Channels::model()->findAll(array(
				'select'=>'ChannelId, ChannelName', 'condition'=>" status='ACTIVE '"));
		$channels    = CHtml::listData($_channels, 'ChannelId',  'ChannelName');
    		
    		//provider
    		$dataProvider = new CActiveDataProvider('Coupon', array(
				'criteria'=>$criteria ,
			));
    		
    		
    		
		
		//send it
		$dataProvider = new CActiveDataProvider('Raffle', array(
			'criteria'=>$criteria ,
		));
		
		$mapping =  array(
			'Brands'       => $brands,
			'Campaigns'    => $campaigns,
			'Clients'      => $clients,
			'Channels'     => $channels,
		);
		
		$this->render('pending',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
		));
	}
	
		

	/**
	* approve via API.
	*/
	public function actionApprove()
	{
	
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		if($search) $criteria->compare('Source', $search, true);

		//all-pending
		$criteria->addCondition("t.Status IN ('ACTIVE','PENDING') ");
		
		//important
		//brands		
		$_brands = Brands::model()->findAll(array(
				'select'=>'BrandId, BrandName', 'condition'=>" status='ACTIVE '"));
		$brands = CHtml::listData($_brands, 'BrandId', 'BrandName');

		//campaigns
		$_campaigns = Campaigns::model()->findAll(array(
			     'select'=>'CampaignId, CampaignName', 'condition'=>" status='ACTIVE '"));
		$campaigns  = CHtml::listData($_campaigns, 'CampaignId', 'CampaignName');

		//clients		
		$_clients   = Clients::model()->findAll(array(
				'select'=>'ClientId, CompanyName', 'condition'=>" status='ACTIVE '"));
		$clients    = CHtml::listData($_clients, 'ClientId',  'CompanyName');
		
		//channels
		$_channels   = Channels::model()->findAll(array(
				'select'=>'ChannelId, ChannelName', 'condition'=>" status='ACTIVE '"));
		$channels    = CHtml::listData($_channels, 'ChannelId',  'ChannelName');
		
		$mapping =  array(
			'Brands'       => $brands,
			'Campaigns'    => $campaigns,
			'Clients'      => $clients,
			'Channels'     => $channels,
		);	
		
		//statys msg
		$this->statusMsg = '';
		$apiUtils  = new Utils;
		$uid       = trim(Yii::app()->request->getParam('uid'));
		$model     = Raffle::model()->findByPk($uid);
		//chk
		if(Yii::app()->user->AccessType !== "SUPERADMIN")
		{
		    $this->statusMsg = Yii::app()->params['notAllowedStatus'];
		}
		else
		{
		    
		    $api   = array(
		    		'data' => array('raffle_id'     => $uid, 
		    				'status'        => $model->Status,
		    				'updated_by'    => $model->UpdatedBy,
		    				'source'        => $model->Source,
		    				'no_of_winners' => $model->NoOfWinners,
		    				'draw_date'     => $model->DrawDate,
		    			        'update_raffle' => true),
		    		'url'  => Yii::app()->params['api-url']['update_raffle'],
		    		);
		    $ret   = $apiUtils->send2Api($api);
		    
		    $this->statusMsg = ( ( $ret["result_code"] == 200) ?
		                       ( 'Successfully generated the raffle.' ) :
		                       ( sprintf("Error occurred while generating the  raffle.<br/><br/>[%s]",trim($ret["error_txt"]))) );
		}
		
		//provider
    		$dataProvider = new CActiveDataProvider('Raffle', array(
				'criteria'  =>$criteria ,
			));		
			
		$this->render('pending',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
			));			
	}



	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Raffle the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Raffle::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Raffle $model the model to be validated
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
