<?php

class PointsController extends Controller
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','new','index','view'),
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
		$model=new Points;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		if(isset($_POST['Points']))
		{

			$params = $_POST['Points'];

			if(! isset($params['CampaignId'])) $params['CampaignId'] = array();
			if(! isset($params['ChannelId'])) $params['ChannelId'] = array();

			$arr_campaigns = $params['CampaignId'];
			$arr_channels  = $params['ChannelId'];
			$params['BrandId'] = (int) $params['BrandId'];

			// if(Yii::app()->user->AccessType !== "SUPERADMIN" && $model->scenario === 'insert') {
			
			
			if(Yii::app()->utils->getUserInfo('AccessType') === 'ADMIN')
			{
				$client_id = Yii::app()->user->ClientId;
				
			} else {
				$client_id = $_POST['Points']['ClientId'];
			}

			/**
			 * Preliminary validation
			 */
			if(empty($client_id)) $model->addError('ClientId', 'Client cannot be blank.');
			if(empty($params['BrandId'])) $model->addError('BrandId', 'Brand cannot be blank.');
			if(empty($arr_campaigns)) $model->addError('CampaignId', 'Campaign cannot be blank.');
			if(empty($arr_channels))  $model->addError('ChannelId', 'Channel cannot be blank.');
			// End validation

			if(!$model->hasErrors())
			{
				unset($params['CampaignId'], $params['ChannelId']);

				$transaction = Yii::app()->db->beginTransaction();

				try {
					foreach($arr_campaigns as $row_campaign) {
						foreach($arr_channels as $row_channel) {
							list($ChannelCampaignId, $LoopChannelId) = explode('-', $row_channel);
							if ($ChannelCampaignId != $row_campaign) continue;
							else
							{
								$model = new Points;
								$model->attributes=$params;
								$model->BrandId = $params['BrandId'];
								$model->CampaignId=$row_campaign;
								$model->ChannelId=$LoopChannelId;
								$model->ClientId = $client_id; // this will change if SUPERADMIN
								$model->Status='ACTIVE';
								$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
								$model->setAttribute("CreatedBy", Yii::app()->user->id);
								$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
								$model->setAttribute("UpdatedBy", Yii::app()->user->id);
								$model->save();
							}
							
							/*
							$model = new Points;
							$model->attributes=$params;
							$model->BrandId = $params['BrandId'];
							$model->CampaignId=$row_campaign;
							$model->ChannelId=$row_channel;
							$model->Status='ACTIVE';
							$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
							$model->setAttribute("CreatedBy", Yii::app()->user->id);
							$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
							$model->setAttribute("UpdatedBy", Yii::app()->user->id);
							$model->save();
							*/
						}
					}
					if(!$model->hasErrors()) {
						$transaction->commit();
						$message = "Points created. " . CHtml::link('View', $this->createUrl('/points'));
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

		$_brands = Brands::model()->thisClient()->findAll(array(
			'select'=>'BrandId, BrandName', 'condition'=>'status=\'ACTIVE\''));
		$brands = CHtml::listData($_brands, 'BrandId', 'BrandName');
		
		$_campaigns = Campaigns::model()->findAll(array(
			'select'=>'CampaignId, BrandId, CampaignName', 'condition'=>'status=\'ACTIVE\''));
		$campaigns = CHtml::listData($_campaigns, 'CampaignId', 'CampaignName');
		
		$_channels = Channels::model()->with('channelCampaigns')->findAll(array('condition'=>'t.status=\'ACTIVE\''));
			// 'select'=>'ChannelId, BrandId, CampaignId, ChannelName, champaigns.CampaignName', 'condition'=>'t.status=\'ACTIVE\''));
			
			/*
	    $_channels = Channels::model()->with('channelCampaigns')->findAll(array(
			'select'=>'t.ChannelId, t.BrandId, t.CampaignId, t.ChannelName, c.CampaignName', 'condition'=>'t.status=\'ACTIVE\'', 'join'=>'left join campaigns c using(CampaignId)'));
			*/
		
		$channels = array(); //CHtml::listData($_channels, 'ChannelId', 'ChannelName');
		foreach($_channels as $row) {
			$channels[$row->ChannelId] = "{$row->ChannelName} ({$row->channelCampaigns->CampaignName})";
		}
		// echo '<pre>';
		// print_r($_channels);
			// print_r($_channels[0]->channelCampaigns['CampaignName']);
		//print_r($channels);
		// exit();
		
		$_rewardslist = RewardsList::model()->findAll(array(
			'select'=>'RewardId, Title', 'condition'=>'status=\'ACTIVE\''));
		$rewardslist = CHtml::listData($_rewardslist, 'RewardId', 'Title');

		// echo '<pre>';
		// print_r($channels);
		// exit();

		$form_view = $this->renderPartial('_form', array(
			'model'=>$model,
			'brands_list'=>$brands,
			'channels_list'=>$channels,
			'campaigns_list'=>$campaigns,
			'rewards_list'=>$rewardslist,
		), true);

		$this->render('create',array(
			'formView'=>$form_view,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionNew()
	{
		$model=new Points;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		$_brands = Brands::model()->thisClient()->findAll(array(
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

		if(isset($_POST['Points']))
		{
			// echo "<pre>"; var_dump($_POST); exit;
			$model->attributes=$_POST['Points'];
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy", Yii::app()->user->id);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			
			if($model->save())
				$this->redirect(array('view','id'=>$model->PointsId));
		}

		$this->render('create2',array(
			'model'=>$model,
			'brand_id'=>$brands,
			'channel_id'=>$channels,
			'campaign_list'=>$campaigns,
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

		if(isset($_POST['Points']))
		{
			$model->attributes=$_POST['Points'];
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save())
				$this->redirect(array('view','id'=>$model->PointsId));
		}

		$this->render('update',array(
			'model'=>$model,
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

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		/*
		$dataProvider=new CActiveDataProvider('Points');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
		*/
		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN') {
			$dataProvider = new CActiveDataProvider('Points');
		} else {
			$dataProvider = new CActiveDataProvider('Points', array(
				'criteria'=>array(
				    'scopes'=>array('thisClient'),
				),
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
		$model=new Points('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Points']))
			$model->attributes=$_GET['Points'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Points the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Points::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Points $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='points-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
