<?php

class PointsLogController extends Controller
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
			// 'postOnly + delete', // we only allow deletion via POST request
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
			array('allow',
				'users'=>array('@')
				),
			array('deny'),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		//echo "<hr>[$id]actionView: " .@var_export($this->loadModel($id),true);
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($id)
	{
		$log_id = Yii::app()->getRequest()->getParam('id');
		
		// get details
		$model=PointsLog::model()->findByPk($log_id);
		
		// get point value
		$modelpt=Points::model()->findByPk($model->PointsId);
		
		$params = array('subscription_id' => $model->SubscriptionId,
						'channel_id' => $model->ChannelId, 'campaign_id' => $model->CampaignId, 'brand_id' => $model->BrandId,
						'points_id' => $model->PointsId, 'points' => $modelpt->Value, 'client_id' => $model->ClientId, 'customer_id' => $model->CustomerId, 'action' => 'ADD');
						
		$url = Yii::app()->params['updatePoints'];
		$output = Yii::app()->curl->post($url, $params);
		
		$this->redirect(array('/pointsLog/?points_id=' . $model->PointsId));
		/*
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PointsLog']))
		{
			$model->attributes=$_POST['PointsLog'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->PointLogId));
		}

		$this->render('create',array(
			'model'=>$model,
		));
		*/
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

		if(isset($_POST['PointsLog']))
		{
			$model->attributes=$_POST['PointsLog'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->PointLogId));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	public function actionRemove($id)
	{
		$url = Yii::app()->params['updatePoints'];
		$model=PointsLog::model()->findByPk($id);
		$params = array('point_log_id' => $model->PointLogId, 'subscription_id' => $model->SubscriptionId,
						'channel_id' => $model->ChannelId, 'campaign_id' => $model->CampaignId, 'brand_id' => $model->BrandId,
						'points_id' => $model->PointsId, 'client_id' => $model->ClientId, 'action' => 'SUBTRACT');
		$output = Yii::app()->curl->post($url, $params);
		
		// $this->redirect(Yii::app()->createUrl('/pointsLog/?points_id=' . $model->PointsId));
		$this->redirect(array('/pointsLog/?points_id=' . $model->PointsId));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		//$this->loadModel($id)->delete();
		$url = Yii::app()->params['updatePoints'];
		
		$utilLog = new Utils;
		$utilLog->saveAuditLogs();

		/*echo $url;
		exit();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		*/
	}
	
	public function actionPointid($points_id=0)
	{
		$pointsId = Yii::app()->getRequest()->getParam('points_id');
		$criteria = new CDbCriteria;
		$criteria->addCondition('SubscriptionId = :pointsId');
		$criteria->addCondition('ClientId = :clientId');
		$criteria->params = array(':pointsId' => $pointsId, ':clientId' => Yii::app()->user->ClientId);
		$dataProvider = new CActiveDataProvider('PointsLog', array(
				'criteria'=> $criteria,
			));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new PointsLog('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PointsLog']))
		$model->attributes=$_GET['PointsLog'];

		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		if(strlen($search))
		{
			$criteria->with = array(
				'pointlogChannels' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" pointlogChannels.ChannelName LIKE '%".addslashes($search)."%' ");
			$criteria->addCondition('t.ClientId = :clientId');
			$criteria->params = array(':clientId' => Yii::app()->user->ClientId);
		}
		else
		{
			$criteria->scopes = array('thisClient');
		}
		
		$dataProvider = new CActiveDataProvider('PointsLog', array(
				'criteria'=>$criteria,
			));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new PointsLog('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PointsLog']))
			$model->attributes=$_GET['PointsLog'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return PointsLog the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=PointsLog::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param PointsLog $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='points-log-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionCreate1()
	{
		$model=new PointsLog;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$_brands = Brands::model()->findAll(array(
			  'select'=>'BrandId, BrandName', 'condition'=>'status=\'ACTIVE\''));
		$brands = CHtml::listData($_brands, 'BrandId', 'BrandName');

		$_campaigns = Campaigns::model()->findAll(array(
			   'select'=>'CampaignId, BrandId, CampaignName', 'condition'=>'status=\'ACTIVE\''));
		$campaigns = CHtml::listData($_campaigns, 'CampaignId', 'CampaignName');

		$_channels = Channels::model()->with('channelCampaigns')->findAll(array('condition'=>'t.status=\'ACTIVE\''));
		$channels  = array();
		foreach($_channels as $row) {
			$channels[$row->ChannelId] = "{$row->ChannelName} ({$row->channelCampaigns->CampaignName})";
		}
		$_cust = Customers::model()->findAll(array(
			'select'=>'CustomerId, FirstName, LastName', 'condition'=>'status=\'ACTIVE\''));
		$cust  = array();
		foreach($_cust as $row) {
			$cust[$row->CustomerId] = "{$row->FirstName} {$row->LastName}";
		}
		
		
		
		//POST - DATA
		if(isset($_POST['PointsLog']))
		{
			$model->attributes=$_POST['PointsLog'];
			//echo "CUSTOMER-SUBSCRIPTION:<hr>".@var_export($_POST['PointsLog'],true);
			
			
			$pPoints     = ($_POST['PointsLog']['Points'])    ?($_POST['PointsLog']['Points']):('');
			$pBrandId    = ($_POST['PointsLog']['BrandId'])   ?($_POST['PointsLog']['BrandId']):('');
			$pCampaignId = ($_POST['PointsLog']['CampaignId'])?($_POST['PointsLog']['CampaignId']):('');
			$pChannelId  = ($_POST['PointsLog']['ChannelId']) ?($_POST['PointsLog']['ChannelId']):('');
			$pCustomerId = ($_POST['PointsLog']['CustomerId'])?($_POST['PointsLog']['CustomerId']):('');
			$pClientId   = trim(Yii::app()->user->ClientId);
			$modErr      = 0;
			
			
			if($pCustomerId <= 0 or $pCustomerId=='')
			{
			   $model->addError('CustomerId', 'Customer Name cannot be blank.');
			   $modErr++;
			}
			if($pBrandId <= 0 or $pBrandId=='')
			{
			   $model->addError('BrandId', 'Brand Name cannot be blank.');
			   $modErr++;
			}
			if($pCampaignId <= 0 or $pCampaignId=='')
			{
			   $model->addError('CampaignId', 'Campaign Name cannot be blank.');
			   $modErr++;
			}
			if($pChannelId <= 0 or $pChannelId=='')
			{
			   $model->addError('ChannelId', 'Channel Name cannot be blank.');
			   $modErr++;
			}
			if(!@preg_match('/^(-)?[0-9]+$/i',$pPoints) or $pPoints=='')
			{
			   $model->addError('Points', 'Points Value cannot be blank.');
			   $modErr++;
			}
			
			
			if($modErr>0)
			{
				$this->render('create1',array(
				'model'=>$model,
				'customer_list' => $cust,
				'brand_list'    => $brands,
				'campaign_list' => $campaigns,
				'channel_list'  => $channels,
				));
				return;
			}
			

			
			
			//CUSTOMER-SUBSCRIPTION
			$criteria = new CDbCriteria;
			$criteria->addCondition('CustomerId = :customer_Id' );
			$criteria->addCondition('ClientId   = :client_Id'   );
			$criteria->addCondition('BrandId    = :brand_Id'    );
			$criteria->addCondition('CampaignId = :campaign_Id' );
			$criteria->addCondition('ChannelId  = :channel_Id'  );
			$criteria->offset = 0;
			$criteria->limit  = 1;
			

			
			$dataProvider = array();
		 	
			$criteria->params = array(
				':customer_Id' => $pCustomerId, 
				':client_Id'   => $pClientId,
				':brand_Id'    => $pBrandId,
				':campaign_Id' => $pCampaignId,
				':channel_Id'  => $pChannelId,
				);
				
			$dataProvider = new CActiveDataProvider('CustomerSubscriptions', array(
				'criteria'=> $criteria,
			));
			


			//echo "<hr>] 5a CUSTOMER-SUBSCRIPTION:<hr>".@var_export($_POST['PointsLog'],true);
			
			$custsub = ($dataProvider != null)?(@count($dataProvider->data)):(0);
			
			//echo "<hr>] 5b CUSTOMER-SUBSCRIPTION: $custsub<hr>".@var_export($dataProvider->data[0]->attributes['SubscriptionId'],true);
			
			$subId = 0;
			if( $custsub <= 0)
			{
			
			
			
				$cmodel=new CustomerSubscriptions;
				$cmodel->attributes = array(
					"CustomerId" => $pCustomerId,
					"BrandId"    => $pBrandId,
					"ClientId"   => $pClientId,
					"CampaignId" => $pCampaignId,
					"ChannelId"  => $pChannelId,
					"Status"     => "Active",

				);

				$cmodel->setAttribute("DateCreated",new CDbExpression('NOW()'));
				$cmodel->setAttribute("CreatedBy", Yii::app()->user->id);
				$cmodel->setAttribute("DateUpdated",new CDbExpression('NOW()'));
				$cmodel->setAttribute("UpdatedBy", Yii::app()->user->id);
				if(!$cmodel->save())
				{
					$nop = 1;
				}
				$subId = $cmodel->SubscriptionId;
				//echo "<pre>NEW=SUBSCRIPTION: $subId<hr></pre>";
			}
			else
			{
				$subId = $dataProvider->data[0]->attributes['SubscriptionId'];
				//echo "<pre>OLD=SUBSCRIPTION: $subId<hr></pre>";
			}
			


			//CUSTOMER-SUBSCRIPTION
			
			//chk the SUBSCRIPTION_ID -> CUSTOMER_POINTS
			if($subId > 0)
			{
				$scriteria = new CDbCriteria;
				$scriteria->addCondition('SubscriptionId = :subscription_Id' );
				$scriteria->params = array(
					':subscription_Id' => $subId, 
				);
				$scriteria->offset = 0;
				$scriteria->limit  = 1;

				$sdataProvider = new CActiveDataProvider('CustomerPoints', array(
					'criteria'=> $scriteria,
				));
				
				$scustomerPts = ($sdataProvider != null)?(@count($sdataProvider->data)):(0);
				
				//echo "<pre>CUSTOMER-PTS-TOTAL: $scustomerPts<hr>".@var_export($sdataProvider,true)."</pre>";
				if($scustomerPts <= 0)
				{
					$cmodel=new CustomerPoints;
					$cmodel->attributes = array(
						"SubscriptionId" => $subId,
						"Used"           => 0,
						"Balance"        => 0,
						"Total"          => $pPoints,
					);
					$cmodel->setAttribute("DateCreated",new CDbExpression('NOW()'));
					$cmodel->setAttribute("CreatedBy", Yii::app()->user->id);
					$cmodel->setAttribute("DateUpdated",new CDbExpression('NOW()'));
					$cmodel->setAttribute("UpdatedBy", Yii::app()->user->id);
					$seqId = 0;
					if($cmodel->save())
					{
					   $seqId = $cmodel->CustomerPointId;
					}
					//echo "<pre>CUSTOMER_POINTS-SEQ: $seqId<hr></pre>";
				}
				else
				{
					$seqId  = $sdataProvider->data[0]->attributes['SubscriptionId'];
					$cusId  = $sdataProvider->data[0]->attributes['CustomerPointId'];
					$cmodel = CustomerPoints::model()->findByPk($cusId);
					
					//echo "<pre>OLD-=CUSTOMER_POINTS-SEQ: $seqId<hr></pre>";
					$cmodel->attributes = array(
						"SubscriptionId" => $seqId,
						"Used"           => 0,
						"Balance"        => 0,
						"Total"          => $pPoints,
					);
					$cmodel->setAttribute("DateUpdated",new CDbExpression('NOW()'));
					$cmodel->setAttribute("UpdatedBy", Yii::app()->user->id);
					$seqId = 0;
					if($cmodel->save())
					{
						$nop =1;
					//	echo "<pre>UPDATED-=CUSTOMER_POINTS-SEQ: $cusId<hr></pre>";
					}
				}
			}//chk the SUBSCRIPTION_ID -> CUSTOMER_POINTS
			
			//SAVE to POINTS_LOG
			if('POINTS_LOG' == 'POINTS_LOG' && $subId > 0)
			{
				$cmodel=new PointsLog;
				$cmodel->attributes = array(
					"SubscriptionId" => $subId,
					"CustomerId" => "$pCustomerId",
					"ClientId"   => "$pClientId",
					"BrandId"    => "$pBrandId",
					"CampaignId" => "$pCampaignId",
					"ChannelId"  => "$pChannelId",
					//"PointsId"   => 0,
					"Points"     => $pPoints,
				);
				$cmodel->setAttribute("DateCreated",new CDbExpression('NOW()'));
				$cmodel->setAttribute("CreatedBy", Yii::app()->user->id);
				$seqId = 0;
				if($cmodel->save())
				{
				   $seqId = $cmodel->PointLogId;
				   $this->redirect(array('view','id'=>$seqId));
				}
				//echo "<pre>POINTS_LOG-SEQ: $seqId<hr>";
			}
			//SAVE to POINTS_LOG
			
		}// POST-DATA
		
		
		$this->render('create1',array(
			'model'=>$model,
			'customer_list' => $cust,
			'brand_list'    => $brands,
			'campaign_list' => $campaigns,
			'channel_list'  => $channels,
		));
	}

}
