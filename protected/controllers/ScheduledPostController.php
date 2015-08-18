<?php

class ScheduledPostController extends Controller
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
				'actions'=>array('index','view','create','update',
				'list','delete','getrewardlist','getcouponlist','getpointlist'),
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

		$model = ScheduledPost::model()->findAllByAttributes($criteria);

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
		$model = new ScheduledPost;

		// Uncomment the following line if AJAX validation is needed
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

		
		if(isset($_POST['ScheduledPost']))
		{
			$model->attributes=$_POST['ScheduledPost'];
			if(Yii::app()->user->AccessType !== "SUPERADMIN" && $model->scenario === 'insert') {
				$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			}


			
			//reset the campaignId
			$chans = @preg_split("/[\-]/", trim($_POST['ScheduledPost']['ChannelId']));
			if(@count($chans)>=2)
			$model->setAttribute("ChannelId", $chans[1]);
			$model->setAttribute("Status", 'ACTIVE');
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy", Yii::app()->user->id);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			

			$AwardType = trim($_POST['ScheduledPost']['AwardType']);
			
			$PointsId  = (int) trim($_POST['ScheduledPost']['PointsId']);
			$CouponId  = (int) trim($_POST['ScheduledPost']['CouponId']);
			$RewardId  = (int) trim($_POST['ScheduledPost']['RewardId']);
			
			$model->setAttribute("PointsId", $PointsId);
			$model->setAttribute("CouponId", $CouponId);
			$model->setAttribute("RewardId", $RewardId);
			$try2Chk   = 0;
			
			if(@preg_match("/(POINT|COUPON|REWARD)/",$AwardType))
			{
				if('POINT' == $AwardType && $PointsId <=0 )
				{
				    $model->addError('error', 'Must select a valid Points');
				    $try2Chk   = 1;
				}
				else if('COUPON' == $AwardType && $CouponId <=0 )
				{
				    $model->addError('error', 'Must select a valid Coupon');
				    $try2Chk   = 1;
				}
				else if('REWARD' == $AwardType && $RewardId <=0 )
				{
				    $model->addError('error', 'Must select a valid Reward');
				    $try2Chk   = 1;
				}
			}
			else
			{
				$AwardType = 'NONE';
			}
			
			$model->setAttribute("AwardType",$AwardType);
			$model->setAttribute("EventType", trim($_POST['ScheduledPost']['EventType']));
			$model->setAttribute("RepeatType",trim($_POST['ScheduledPost']['RepeatType']));
			
			if($try2Chk <=0 && $model->save())
			{
				$this->redirect(array('view','id'=>$model->SchedId));
			}
			else
			{
				Yii::app()->user->setFlash('error', 'An unexpected error occured.');
			}
			
		
		}
		
		$brand_list = (Yii::app()->user->AccessType !== "SUPERADMIN") ? $this->getBrands(Yii::app()->user->ClientId) : (array());

		
		$this->render('create',array(
			'model'      => $model,
			'client_list'=> $clients,
			'brand_list' => $brand_list,
			'channel_list'  => array(),
			'campaign_list' => array(),
			'point_list'    => array(),
			'coupon_list'   => array(),
			'reward_list'   => array(),
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

		$_clients = Clients::model()->findAll(array(
			'select'=>'ClientId, CompanyName', 'condition'=>'status=\'ACTIVE\''));
		$clients = array();
		foreach($_clients as $row) {
			$clients[$row->ClientId] = $row->CompanyName;

		}

		if(isset($_POST['ScheduledPost']))
		{
			$model->attributes=$_POST['ScheduledPost'];
			
			$chans = @preg_split("/[\-]/", trim($_POST['ScheduledPost']['ChannelId']));
			if(@count($chans)>=2)
			$model->setAttribute("ChannelId", $chans[1]);
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			
			
			$AwardType = trim($_POST['ScheduledPost']['AwardType']);
			$PointsId  = (int) trim($_POST['ScheduledPost']['PointsId']);
			$CouponId  = (int) trim($_POST['ScheduledPost']['CouponId']);
			$RewardId  = (int) trim($_POST['ScheduledPost']['RewardId']);

			$model->setAttribute("PointsId", $PointsId);
			$model->setAttribute("CouponId", $CouponId);
			$model->setAttribute("RewardId", $RewardId);
			$try2Chk   = 0;

			if(@preg_match("/(POINT|COUPON|REWARD)/",$AwardType))
			{
				if('POINT' == $AwardType && $PointsId <=0 )
				{
				    $model->addError('error', 'Must select a valid Points');
				    $try2Chk   = 1;
				}
				else if('COUPON' == $AwardType && $CouponId <=0 )
				{
				    $model->addError('error', 'Must select a valid Coupon');
				    $try2Chk   = 1;
				}
				else if('REWARD' == $AwardType && $RewardId <=0 )
				{
				    $model->addError('error', 'Must select a valid Reward');
				    $try2Chk   = 1;
				}
			}
			else
			{
				$AwardType = 'NONE';
			}
			$model->setAttribute("AwardType",$AwardType);
			$model->setAttribute("EventType", trim($_POST['ScheduledPost']['EventType']));
			$model->setAttribute("RepeatType",trim($_POST['ScheduledPost']['RepeatType']));

			
			if($try2Chk <=0 && $model->save())
				$this->redirect(array('view','id'=>$model->SchedId));
		}

		$this->render('update',array(
			'model'=>$model,
			'client_list'   => $clients,
			'brand_list'    => $this->getBrands($model->ClientId),
			'campaign_list' => $this->getCampaigns($model->BrandId),
			'channel_list'  => $this->getChannels($model->BrandId,$model->CampaignId),
			'point_list'    => $this->actionGetpointlist2($model->BrandId,
							$model->CampaignId,
							$model->ChannelId),
			'coupon_list'   => $this->actionGetcouponlist2($model->BrandId,
							$model->CampaignId,
							$model->ChannelId),
			'reward_list'   => $this->actionGetRewardlist2($model->BrandId,
							$model->CampaignId,
							$model->ChannelId),
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$model    = $this->loadModel($id);
		$rowCount = $model->findByPk($id)->delete();
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		
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
				'schedBrands' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" (
			 	schedBrands.BrandName     LIKE '%".addslashes($search)."%' 
			 ) ");
		}			

		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN') {
			$dataProvider = new CActiveDataProvider('ScheduledPost', array(
				'criteria'=>$criteria ,
			));
		} else {
			$criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
			$dataProvider = new CActiveDataProvider('ScheduledPost', array(
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
		$model=new ScheduledPost('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ScheduledPost']))
			$model->attributes=$_GET['ScheduledPost'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ScheduledPost the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=ScheduledPost::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param ScheduledPost $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='brands-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function getMoreLists()
	{

		//brands		
		$_brands = Brands::model()->findAll(array(
				'select'=>'BrandId, BrandName', 'condition'=>" status='ACTIVE'"));
		$brands = CHtml::listData($_brands, 'BrandId', 'BrandName');

		//campaigns
		$_campaigns = Campaigns::model()->findAll(array(
			     'select'=>'CampaignId, CampaignName', 'condition'=>" status='ACTIVE'"));
		$campaigns  = CHtml::listData($_campaigns, 'CampaignId', 'CampaignName');

		//clients		
		$_clients   = Clients::model()->findAll(array(
				'select'=>'ClientId, CompanyName', 'condition'=>" status='ACTIVE'"));
		$clients    = CHtml::listData($_clients, 'ClientId',  'CompanyName');

		//channels
		$_channels   = Channels::model()->findAll(array(
				'select'=>'ChannelId, ChannelName', 'condition'=>" status='ACTIVE'"));
		$channels    = CHtml::listData($_channels, 'ChannelId',  'ChannelName');

		$_channels   = Channels::model()->findAll(array(
				'select'=>'ChannelId, ChannelName', 'condition'=>" status='ACTIVE'"));
		$channels    = CHtml::listData($_channels, 'ChannelId',  'ChannelName');


		//customers
		$clid   = addslashes(Yii::app()->user->ClientId);
		$cand   = (Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  ? (" AND ClientId='$clid' ") : ('');
		$_customers = Customers::model()->findAll(array(
				'select'=>'CustomerId, Email', 'condition'=>" status='ACTIVE' $cand "));
		$customers = CHtml::listData($_customers, 'CustomerId', 'Email');
		
		//give mapping
		return array(
			'Brands'       => $brands,
			'Campaigns'    => $campaigns,
			'Clients'      => $clients,
			'Channels'     => $channels,
			'custList'     => $customers
			);
	}




    protected function getBrands($ClientId=0)
    {
    	
        $model = Brands::model()->findAllByAttributes(array('ClientId'=>$ClientId), array('select'=>'BrandId, BrandName'));
        $list  = CHtml::listData($model, 'BrandId', 'BrandName');
        return $list;
    }

    protected function getCampaigns($BrandId=0)
    {
        if( ! (intval($BrandId)) ) return Yii::app()->utils->sendJSONResponse(array());

        $model = Campaigns::model()->findAllByAttributes(array('BrandId'=>$BrandId), array('select'=>'CampaignId, CampaignName'));
        $list  = array();

        foreach($model as $row) { $list[$row['CampaignId']] = $row['CampaignName']; }
        return $list;
    }

    protected function getChannels($BrandId=0,$CampaignId=0)
    {
        
        $criteria = new CDbCriteria;
        $criteria->condition = "t.Status = 'ACTIVE'";
        $criteria->addCondition('t.BrandId = :filter_brand_id');
        $criteria->params[':filter_brand_id'] = $BrandId;

        if(is_array($CampaignId)) { 
        	$criteria->addInCondition('t.CampaignId', $CampaignId); 
        }
        else { 
            $criteria->addCondition('t.CampaignId = :filter_campaign_id'); 
            $criteria->params[':filter_campaign_id'] = (int) $CampaignId;
        }

        $model = Channels::model()->with('channelCampaigns')->findAll($criteria);
        $list = array();

		foreach($model as $row) {
			$list[$row->CampaignId.'-'.$row->ChannelId] = "{$row->ChannelName} ({$row->channelCampaigns->CampaignName})";
		}
		
		return $list;
    	}




	public function actionGetRewardlist()
	{

		$criteria = new CDbCriteria;

		$xtra1  = ''; 
		$xtra2  = '';
		$xtra3  = '';
		$xtra4  = '';

		$BrandId    = trim(Yii::app()->request->getParam('BrandId') );
		$CampaignId = trim(Yii::app()->request->getParam('CampaignId'));
		$ChannelId  = trim(Yii::app()->request->getParam('ChannelId'));
		
		
		$chans = @preg_split("/[\-]/", trim($ChannelId));
		if(@count($chans)>=2)
			$ChannelId = $chans[1];

		//clientid
		if(Yii::app()->user->AccessType !== "SUPERADMIN") 
		{
			$tid   = addslashes(Yii::app()->user->ClientId);
			$xtra1 = " AND t.ClientId = '$tid' ";
		}
		
		//brand
		$tid   = addslashes($BrandId);
		$xtra2 = " AND t.BrandId = '$tid' ";
		
		//camp
		$tid   = addslashes($CampaignId);
		$xtra3 = " AND t.CampaignId = '$tid' ";

		//chan
		$tid   = addslashes($ChannelId);
		$xtra4 = " AND t.ChannelId = '$tid' ";


        	$list  = array();

		if(1){
		
			$rawSql = "
			SELECT  t.*
			FROM rewards_list t
			WHERE 1=1 
				$xtra1 
				$xtra2
				$xtra3
				$xtra4
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField' => 'RewardId',
				    'totalItemCount' => $rawCount,
				    )
				);

		}
		
		foreach($dataProvider->getData() as $row)
		{
			$list[$row["RewardId"]] = $row["Title"];
		}
		
		//give
		Yii::app()->utils->sendJSONResponse($list);
	}


	public function actionGetcouponlist()
	{

		$criteria = new CDbCriteria;

		$xtra1  = ''; 
		$xtra2  = '';
		$xtra3  = '';
		$xtra4  = '';

		$BrandId    = trim(Yii::app()->request->getParam('BrandId') );
		$CampaignId = trim(Yii::app()->request->getParam('CampaignId'));
		$ChannelId  = trim(Yii::app()->request->getParam('ChannelId'));
		
		
		$chans = @preg_split("/[\-]/", trim($ChannelId));
		if(@count($chans)>=2)
			$ChannelId = $chans[1];

		//clientid
		if(Yii::app()->user->AccessType !== "SUPERADMIN") 
		{
			$tid   = addslashes(Yii::app()->user->ClientId);
			$xtra1 = " AND t.ClientId = '$tid' ";
		}
		
		//brand
		$tid   = addslashes($BrandId);
		$xtra2 = " AND t.BrandId = '$tid' ";
		
		//camp
		$tid   = addslashes($CampaignId);
		$xtra3 = " AND t.CampaignId = '$tid' ";

		//chan
		$tid   = addslashes($ChannelId);
		$xtra4 = " AND t.ChannelId = '$tid' ";


        	$list  = array();

		if(1){
		
			$rawSql = "
			SELECT  a.*
			FROM coupon a,
			     coupon_mapping t
			WHERE 1=1 
			      AND a.CouponId = t.CouponId
				$xtra1 
				$xtra2
				$xtra3
				$xtra4
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField' => 'CouponId',
				    'totalItemCount' => $rawCount,
				    )
				);

		}
		
		foreach($dataProvider->getData() as $row)
		{
			$list[$row["CouponId"]] = sprintf("%s - %s - %s",$row["CouponId"],
							$row["Source"],$row["TypeId"]);
		}
		
		//give
		Yii::app()->utils->sendJSONResponse($list);
	}


	public function actionGetpointlist()
	{

		$criteria = new CDbCriteria;

		$xtra1  = ''; 
		$xtra2  = '';
		$xtra3  = '';
		$xtra4  = '';

		$BrandId    = trim(Yii::app()->request->getParam('BrandId') );
		$CampaignId = trim(Yii::app()->request->getParam('CampaignId'));
		$ChannelId  = trim(Yii::app()->request->getParam('ChannelId'));
		
		
		$chans = @preg_split("/[\-]/", trim($ChannelId));
		if(@count($chans)>=2)
			$ChannelId = $chans[1];

		//clientid
		if(Yii::app()->user->AccessType !== "SUPERADMIN") 
		{
			$tid   = addslashes(Yii::app()->user->ClientId);
			$xtra1 = " AND t.ClientId = '$tid' ";
		}
		
		//brand
		$tid   = addslashes($BrandId);
		$xtra2 = " AND t.BrandId = '$tid' ";
		
		//camp
		$tid   = addslashes($CampaignId);
		$xtra3 = " AND t.CampaignId = '$tid' ";

		//chan
		$tid   = addslashes($ChannelId);
		$xtra4 = " AND t.ChannelId = '$tid' ";


        	$list  = array();

		if(1){
		
			$rawSql = "
				SELECT  t.*
				FROM points t
				WHERE 1=1 
					$xtra1 
					$xtra2
					$xtra3
					$xtra4
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField' => 'PointsId',
				    'totalItemCount' => $rawCount,
				    )
				);

		}
		
		foreach($dataProvider->getData() as $row)
		{
			$list[$row["PointsId"]] = sprintf("%s - %s",$row["PointsId"],
							$row["PointAction"]);
		}
		//give
		Yii::app()->utils->sendJSONResponse($list);
	}




	public function actionGetpointlist2($BrandId=0,$CampaignId=0,$ChannelId)
	{

		$criteria = new CDbCriteria;

		$xtra1  = ''; 
		$xtra2  = '';
		$xtra3  = '';
		$xtra4  = '';
		
		//clientid
		if(Yii::app()->user->AccessType !== "SUPERADMIN") 
		{
			$tid   = addslashes(Yii::app()->user->ClientId);
			$xtra1 = " AND t.ClientId = '$tid' ";
		}
		
		//brand
		$tid   = addslashes($BrandId);
		$xtra2 = " AND t.BrandId = '$tid' ";
		
		//camp
		$tid   = addslashes($CampaignId);
		$xtra3 = " AND t.CampaignId = '$tid' ";

		//chan
		$tid   = addslashes($ChannelId);
		$xtra4 = " AND t.ChannelId = '$tid' ";


        	$list  = array();

		if(1){
		
			$rawSql = "
				SELECT  t.*
				FROM points t
				WHERE 1=1 
					$xtra1 
					$xtra2
					$xtra3
					$xtra4
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField' => 'PointsId',
				    'totalItemCount' => $rawCount,
				    )
				);

		}
		
		foreach($dataProvider->getData() as $row)
		{
			$list[$row["PointsId"]] = sprintf("%s - %s",$row["PointsId"],
							$row["PointAction"]);
		}
		//give
		return $list;
	}

	public function actionGetcouponlist2($BrandId=0,$CampaignId=0,$ChannelId)
	{

		$criteria = new CDbCriteria;

		$xtra1  = ''; 
		$xtra2  = '';
		$xtra3  = '';
		$xtra4  = '';
	
		//clientid
		if(Yii::app()->user->AccessType !== "SUPERADMIN") 
		{
			$tid   = addslashes(Yii::app()->user->ClientId);
			$xtra1 = " AND t.ClientId = '$tid' ";
		}
		
		//brand
		$tid   = addslashes($BrandId);
		$xtra2 = " AND t.BrandId = '$tid' ";
		
		//camp
		$tid   = addslashes($CampaignId);
		$xtra3 = " AND t.CampaignId = '$tid' ";

		//chan
		$tid   = addslashes($ChannelId);
		$xtra4 = " AND t.ChannelId = '$tid' ";


        	$list  = array();

		if(1){
		
			$rawSql = "
			SELECT  a.*
			FROM coupon a,
			     coupon_mapping t
			WHERE 1=1 
			      AND a.CouponId = t.CouponId
				$xtra1 
				$xtra2
				$xtra3
				$xtra4
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField' => 'CouponId',
				    'totalItemCount' => $rawCount,
				    )
				);

		}
		
		foreach($dataProvider->getData() as $row)
		{
			$list[$row["CouponId"]] = sprintf("%s - %s - %s",$row["CouponId"],
							$row["Source"],$row["TypeId"]);
		}
		
		//give
		return $list;
	}

	public function actionGetRewardlist2($BrandId=0,$CampaignId=0,$ChannelId)
	{

		$criteria = new CDbCriteria;

		$xtra1  = ''; 
		$xtra2  = '';
		$xtra3  = '';
		$xtra4  = '';
 

		//clientid
		if(Yii::app()->user->AccessType !== "SUPERADMIN") 
		{
			$tid   = addslashes(Yii::app()->user->ClientId);
			$xtra1 = " AND t.ClientId = '$tid' ";
		}
		
		//brand
		$tid   = addslashes($BrandId);
		$xtra2 = " AND t.BrandId = '$tid' ";
		
		//camp
		$tid   = addslashes($CampaignId);
		$xtra3 = " AND t.CampaignId = '$tid' ";

		//chan
		$tid   = addslashes($ChannelId);
		$xtra4 = " AND t.ChannelId = '$tid' ";


        	$list  = array();

		if(1){
		
			$rawSql = "
			SELECT  t.*
			FROM rewards_list t
			WHERE 1=1 
				$xtra1 
				$xtra2
				$xtra3
				$xtra4
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField' => 'RewardId',
				    'totalItemCount' => $rawCount,
				    )
				);

		}
		
		foreach($dataProvider->getData() as $row)
		{
			$list[$row["RewardId"]] = $row["Title"];
		}
		
		//give
		return $list;
	}


}
