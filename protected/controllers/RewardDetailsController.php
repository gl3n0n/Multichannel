<?php

class RewardDetailsController extends Controller
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
				'actions'=>array('create','update','index','view','getbrands','getcampaigns','getchannels'),
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
		$model=new RewardDetails;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		if(isset($_POST['RewardDetails']))
		{

			$params = $_POST['RewardDetails'];
			// echo '<pre>';
			// print_r($params); exit;

			if(! isset($params['ClientId'])) $params['ClientId'] = array();
			if(! isset($params['BrandId'])) $params['BrandId'] = array();
			if(! isset($params['CampaignId'])) $params['CampaignId'] = array();
			if(! isset($params['ChannelId'])) $params['ChannelId'] = array();
			/*
			if(Yii::app()->user->AccessType !== "SUPERADMIN" && $model->scenario === 'insert') {
				$arr_clients = $params['ClientId'];
			} else {
				$arr_clients = array( Yii::app()->user->ClientId );
			}
			*/
			if(Yii::app()->utils->getUserInfo('AccessType') === 'ADMIN')
			{
				$arr_clients = array( Yii::app()->user->ClientId );
			}
			else
			{
				$arr_clients = $params['ClientId'];
			}
			
			$arr_brands  = $params['BrandId'];
			$arr_campaigns = $params['CampaignId'];
			$arr_channels  = $params['ChannelId'];
			$params['RewardId'] = (int) $params['RewardId'];
			/**
			 * Preliminary validation
			 */
			if(empty($params['RewardId'])) $model->addError('RewardId', 'Reward cannot be blank.');
			if(empty($arr_clients)) $model->addError('ClientId', 'Client cannot be blank.');
			if(empty($arr_brands))  $model->addError('BrandId', 'Brand cannot be blank.');
			if(empty($arr_campaigns)) $model->addError('CampaignId', 'Campaign cannot be blank.');
			if(empty($arr_channels))  $model->addError('ChannelId', 'Channel cannot be blank.');
			// End validation

			// search by channel_id
			//$chann = implode(',', $arr_channels);
			//$test = Channels::model()->findAllByPk('1');
			//$test = Channels::model()->findAll("Channelid IN ({$chann})");
			//echo '<pre>';
			//print_r($test[0]->attributes);
			//print_r($test[0]->ChannelId);
			//exit();


			if( ! $model->hasErrors() )
			{
				unset($params['ClientId'], $params['BrandId'], $params['CampaignId'], $params['ChannelId']);

				$transaction = Yii::app()->db->beginTransaction();
				
				try {
					$chann = implode(',', $arr_channels);
					$coupmap = Channels::model()->findAll("Channelid IN ({$chann})");
	
					for ($i=0; $i<count($coupmap); $i++)
					{
						$brandchann = Brands::model()->findAllByPk($coupmap[$i]->BrandId);
						$clientchann = Clients::model()->findAllByPk($brandchann[0]->ClientId);
						
						$model=new RewardDetails;
						$model->attributes=$params;
						$model->RewardId=$params['RewardId'];
						$model->ClientId=$clientchann[0]->ClientId;
						$model->BrandId=$coupmap[$i]->BrandId;
						$model->CampaignId=$coupmap[$i]->CampaignId;
						$model->ChannelId=$coupmap[$i]->ChannelId;
						$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
						$model->setAttribute("Status", 'ACTIVE');
						$model->setAttribute("CreatedBy", Yii::app()->user->id);
						$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
						$model->setAttribute("UpdatedBy", Yii::app()->user->id);
						$saved = $model->save();
						
					}
					/*
						$brandchann = Brands::model()->findAllByPk($coupmap[$i]->BrandId);
						$clientchann = Clients::model()->findAllByPk($brandchann[0]->BrandId);
						
						$model=new RewardDetails;
						$model->attributes=$params;
						$model->RewardId=$params['RewardId'];
						$model->ClientId=$clientchann[0]->ClientId,
						$model->BrandId=$coupmap[$i]->BrandId,
						$model->CampaignId=$coupmap[$i]->CampaignId,
						$model->ChannelId=$coupmap[$i]->ChannelId,
						$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
						$model->setAttribute("CreatedBy", Yii::app()->user->id);
						$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
						$model->setAttribute("UpdatedBy", Yii::app()->user->id);

						$saved = $model->save();
					}
					*/
					/*
					foreach($arr_clients as $client) {
						foreach($arr_brands as $brand) {
							foreach($arr_campaigns as $campaign) {
								foreach($arr_channels as $channel) {

									$model=new RewardDetails;
									$model->attributes=$params;
									$model->RewardId=$params['RewardId'];
									$model->ClientId=$client;
									$model->BrandId=$brand;
									$model->CampaignId=$campaign;
									$model->ChannelId=$channel;
									$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
									$model->setAttribute("CreatedBy", Yii::app()->user->id);
									$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
									$model->setAttribute("UpdatedBy", Yii::app()->user->id);

									$saved = $model->save();
								}
							}
						}
					}
					*/

					if( !$model->hasErrors() || $saved ) { // Kung walang error
						$transaction->commit();
						$utilLog = new Utils;
						$utilLog->saveAuditLogs();

						$message = "Reward saved. " . CHtml::link('View', $this->createUrl('/rewardDetails'));
						Yii::app()->user->setFlash('success', $message);
					} else {
						$transaction->rollback();
						Yii::app()->user->setFlash('error', 'Unable to save data.');
					}
				} catch(CDbException $ex) {
					$transaction->rollback();
					Yii::app()->user->setFlash('error', 'Error saving data.');
				} catch(Exception $ex) {
					$transaction->rollback();
					Yii::app()->user->setFlash('error', 'An unexpected error occured.');
				}



			}
		}

		$get_active_clause='Status=\'ACTIVE\'';

		$_clients = Clients::model()->active()->findAll(array('select'=>'ClientId, CompanyName'));
		$clients = CHtml::listData($_clients, 'ClientId', 'CompanyName');
		
		$_brandsCriteria = new CDbCriteria;
		$_brands = Brands::model()->thisClient()->active()->findAll(array('select'=>'BrandId, BrandName'));
		
		if(Yii::app()->user->AccessType === "SUPERADMIN")
		$_brands = Brands::model()->active()->findAll(array('select'=>'BrandId, BrandName'));
		$brands = CHtml::listData($_brands, 'BrandId', 'BrandName');
		
		$_campaignsCriteria = new CDbCriteria;
		$_campaignsCriteria->select='CampaignId, CampaignName';
		$_campaigns = Campaigns::model()->thisClient()->active()->findAll($_campaignsCriteria);
		
		if(Yii::app()->user->AccessType === "SUPERADMIN")
		$_campaigns = Campaigns::model()->active()->findAll($_campaignsCriteria);
		
		$campaigns = CHtml::listData($_campaigns, 'CampaignId', 'CampaignName');
		
		// Needs both brand and campaign.
		$_channelsCriteria = new CDbCriteria;
		$_channels = Channels::model()->thisClient()->active()->findAll(array('select'=>'ChannelId, ChannelName'));
		if(Yii::app()->user->AccessType === "SUPERADMIN")
		$_channels = Channels::model()->active()->findAll(array('select'=>'ChannelId, ChannelName'));
		
		$channels = CHtml::listData($_channels, 'ChannelId', 'ChannelName');
		
		$_rewardslistCriteria = new CDbCriteria;
		$_rewardslist = RewardsList::model()->thisClient()->active()->findAll(array('select'=>'RewardId, Title'));
		if(Yii::app()->user->AccessType === "SUPERADMIN")
		$_rewardslist = RewardsList::model()->active()->findAll(array('select'=>'RewardId, Title'));
		
		
		$rewardslist = CHtml::listData($_rewardslist, 'RewardId', 'Title');

		if(0)
		{
			echo 'HEHEHE<hr>'.@var_export($_rewardslist,true);
			echo 'HEHEHE<hr>'.@var_export($rewardslist,true);
			exit;
		}
		$this->render('create',array(
			'model'=>$model,
			'client_list'=>$clients,
			'brand_id'=>$brands,
			'channel_id'=>$channels,
			'campaign_id'=>$campaigns,
			'rewardlist_id'=>$rewardslist,
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
		
		$_clients = Clients::model()->active()->findAll(array(
			'select'=>'ClientId, CompanyName', 'condition'=>'status=\'ACTIVE\''));
		$clients = array();
		foreach($_clients as $row) {
			$clients[$row->ClientId] = $row->CompanyName;

		}
		
		$_brands = Brands::model()->findAll(array(
			'select'=>'BrandId, BrandName', 'condition'=>'status=\'ACTIVE\''));
		$brands = array();
		foreach($_brands as $row) {
			$brands[$row->BrandId] = $row->BrandName;

		}
		
		$_campaigns = Campaigns::model()->findAll(array(
			'select'=>'CampaignId, BrandId, CampaignName', 'condition'=>'status=\'ACTIVE\''));
		$campaigns = array();
		foreach($_campaigns as $row) {
			$campaigns[$row->CampaignId] = $row->CampaignName;

		}
		
		$_channels = Channels::model()->findAll(array(
			'select'=>'ChannelId, BrandId, CampaignId, ChannelName', 'condition'=>'status=\'ACTIVE\''));
		$channels = array();
		foreach($_channels as $row) {
			$channels[$row->ChannelId] = $row->ChannelName;

		}
		
		$_rewardslist = RewardsList::model()->findAll(array(
			'select'=>'RewardId, Title', 'condition'=>'status=\'ACTIVE\''));
		$rewardslist = array();
		foreach($_rewardslist as $row) {
			$rewardslist[$row->RewardId] = $row->Title;

		}

		if(isset($_POST['RewardDetails']))
		{
			$model->attributes=$_POST['RewardDetails'];
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save()){
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();

				$this->redirect(array('view','id'=>$model->RewardConfigId));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'client_list'=>$clients,
			'brand_id'=>$brands,
			'channel_id'=>$channels,
			'campaign_id'=>$campaigns,
			'rewardlist_id'=>$rewardslist,
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
			$criteria->with = array(
				'rdetailChannels' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" rdetailChannels.ChannelName LIKE '%".addslashes($search)."%' ");
		}
		
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')   
		{
			$criteria->compare('ClientId', Yii::app()->user->ClientId, true);    
		}
		$dataProvider = new CActiveDataProvider('RewardDetails', array(
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
		$model=new RewardDetails('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['RewardDetails']))
			$model->attributes=$_GET['RewardDetails'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function actionGetBrands()
	{
		$ClientId = Yii::app()->request->getParam("ClientId", array());

		if( ! is_array($ClientId) && intval($ClientId)) {
			$ClientId = array( intval($ClientId) );
		}

		$criteria = new CDbCriteria;
		$criteria->addInCondition('ClientId', $ClientId);
		$model = Brands::model()->active()->findAll($criteria);

		$list = CHtml::listData($model, 'BrandId', 'BrandName');

		Yii::app()->utils->sendJSONResponse($list);
	}

	public function actionGetCampaigns()
	{
		$BrandId = Yii::app()->request->getParam("BrandId", array());

		if( ! is_array($BrandId) && intval($BrandId)) {
			$BrandId = array( intval($BrandId) );
		}

		$criteria = new CDbCriteria;
		$criteria->addInCondition('BrandId', $BrandId);
		$model = Campaigns::model()->active()->findAll($criteria);

		$list = CHtml::listData($model, 'CampaignId', 'CampaignName');

		Yii::app()->utils->sendJSONResponse($list);
	}

	public function actionGetChannels()
	{
		$BrandId = Yii::app()->request->getParam("BrandId", array());
		$CampaignId = Yii::app()->request->getParam("CampaignId", array());

		if( ! is_array($BrandId) && intval($BrandId)) {
			$BrandId = array( intval($BrandId) );
		}

		if( ! is_array($CampaignId) && intval($CampaignId)) {
			$CampaignId = array( intval($CampaignId) );
		}

		$criteria = new CDbCriteria;
		$criteria->addInCondition('BrandId', $BrandId);
		$criteria->addInCondition('CampaignId', $CampaignId);
		$model = Channels::model()->active()->findAll($criteria);

		$list = CHtml::listData($model, 'ChannelId', 'ChannelName');

		Yii::app()->utils->sendJSONResponse($list);
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return RewardDetails the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=RewardDetails::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param RewardDetails $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='reward-details-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
