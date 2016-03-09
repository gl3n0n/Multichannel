<?php

class SchedEventPostController extends Controller
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
				'list','delete','getrewardlist','getcouponlist','getpointlist','getcustomers','summary'),
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
		if($search) $criteria->compare('Title', $search, true);
		
		$criteria->compare('ClientId', $ClientId, true);
		$criteria->compare('Status',   'active', true);

		$model = SchedEventPost::model()->findAllByAttributes($criteria);

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
		$model = new SchedEventPost;

		// Uncomment the following line if AJAX validation is needed
		$clientsID = Users::model()->findByPk(Yii::app()->user->id)->ClientId;
		if(Yii::app()->user->AccessType === "SUPERADMIN" && $model->scenario === 'insert') {
			$_clients = Clients::model()->active()->findAll();
		} else {
			$_clients = Clients::model()->findAll(array(
				'select'=>'ClientId, CompanyName', 'condition'=>'ClientId='.$clientsID.''));
		}

		$clients  = array();
		foreach($_clients as $row) {
			$clients[$row->ClientId] = $row->CompanyName;

		}

		
		if(isset($_POST['SchedEventPost']))
		{
			$model->attributes=$_POST['SchedEventPost'];
			if(Yii::app()->user->AccessType !== "SUPERADMIN" && $model->scenario === 'insert') {
				$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			}
			
			//reset the campaignId
			$model->setAttribute("Status", 'ACTIVE');
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy", Yii::app()->user->id);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			

			$AwardType = trim($_POST['SchedEventPost']['AwardType']);
			$AwardName = trim($_POST['SchedEventPost']['AwardName']);
			
			$PointsId  = (int) trim($_POST['SchedEventPost']['PointsId']);
			$CouponId  = (int) trim($_POST['SchedEventPost']['CouponId']);
			$RewardId  = (int) trim($_POST['SchedEventPost']['RewardId']);
			
			$model->setAttribute("PointsId", $PointsId);
			$model->setAttribute("CouponId", $CouponId);
			$model->setAttribute("RewardId", $RewardId);
			$try2Chk   = 0;


			if(0)
		        {
			  echo "<pre>".@var_export($_POST,1)."</pre>";
			  exit;

			}
			if(! @preg_match("/^(BIRTHDATE|ANNIVERSARY|NONE)$/",$AwardName))
			{
				$StartDate = trim($_POST['SchedEventPost']['StartDate']);
				$EndDate   = trim($_POST['SchedEventPost']['EndDate']);

				if(!  @preg_match("/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/",$StartDate))
				{
				    $model->addError('error', 'Must select a valid Start Date');
				    $try2Chk   = 1;
				}
				if(!  @preg_match("/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/",$EndDate))
				{
				    $model->addError('error', 'Must select a valid End Date');
				    $try2Chk   = 1;
				}
			}
			
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
			$model->setAttribute("AwardName", trim($_POST['SchedEventPost']['AwardName']));
			
			if($try2Chk <=0 && $model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();

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

		if(isset($_POST['SchedEventPost']))
		{
			$old_attrs = @var_export($model->attributes,1);
			
			$model->attributes=$_POST['SchedEventPost'];
			
			$new_attrs = @var_export($model->attributes,1);
			$audit_logs= sprintf("OLD:\n\n%s\n\nNEW:\n\n%s",$old_attrs,$new_attrs);
			
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			
			
			$AwardType = trim($_POST['SchedEventPost']['AwardType']);
			$PointsId  = (int) trim($_POST['SchedEventPost']['PointsId']);
			$CouponId  = (int) trim($_POST['SchedEventPost']['CouponId']);
			$RewardId  = (int) trim($_POST['SchedEventPost']['RewardId']);

			$model->setAttribute("PointsId", $PointsId);
			$model->setAttribute("CouponId", $CouponId);
			$model->setAttribute("RewardId", $RewardId);
			$try2Chk   = 0;

			if(0)
		        {
			  echo "<pre>".@var_export($_POST,1)."</pre>";
			  exit;

			}

			$AwardName = trim($_POST['SchedEventPost']['AwardName']);
			if(! @preg_match("/^(BIRTHDATE|ANNIVERSARY|NONE)$/",$AwardName))
			{
				$StartDate = trim($_POST['SchedEventPost']['StartDate']);
				$EndDate   = trim($_POST['SchedEventPost']['EndDate']);

				if(!  @preg_match("/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/",$StartDate))
				{
				    $model->addError('error', 'Must select a valid Start Date');
				    $try2Chk   = 1;
				}
				if(!  @preg_match("/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/",$EndDate))
				{
				    $model->addError('error', 'Must select a valid End Date');
				    $try2Chk   = 1;
				}
			}


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
			$model->setAttribute("AwardName", trim($_POST['SchedEventPost']['AwardName']));

			
			if($try2Chk <=0 && $model->save()){
				$utilLog = new Utils;
				$utilLog->saveAuditLogs(null,$audit_logs);

				$this->redirect(array('view','id'=>$model->SchedId));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'client_list'   => $clients,
			'point_list'    => $this->actionGetpointlist2(),
			'coupon_list'   => $this->actionGetcouponlist2(),
			'reward_list'   => $this->actionGetRewardlist2(),
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
		$utilLog = new Utils;
		$utilLog->saveAuditLogs();
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		
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
			$criteria->addCondition(" (
			 	t.Title       LIKE '%$t%'  OR
			 	t.Description LIKE '%$t%'
			 ) ");
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
			$criteria->addCondition(" ( t.StartDate >= '$t 00:00:00' ) ");
		}
		//date: 
		$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
		{
			$t = addslashes($byTranDateTo);
			$criteria->addCondition(" ( t.EndDate <= '$t 23:59:59' ) ");
		}		
		
		//by points
		$byPointsName = trim(Yii::app()->request->getParam('byPointsName'));
		if(strlen($byPointsName))
		{
				$t = addslashes($byPointsName);
				$criteria->addCondition(" ( EXISTS (
						SELECT 1 
						FROM
							points p
						WHERE
							1=1
							and
							p.PointsId = t.PointsId
							and
							p.Name  LIKE '%$t%'
				) ) ");
		}

		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN') {
			$dataProvider = new CActiveDataProvider('SchedEventPost', array(
				'criteria'=>$criteria ,
				'sort'    => array(
										'defaultOrder' => ' t.SchedId DESC ',
										)
				
			));
		} else {
			$criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
			$dataProvider = new CActiveDataProvider('SchedEventPost', array(
				'criteria'=>$criteria ,
				'sort'    => array(
										'defaultOrder' => ' t.SchedId DESC ',
										)
				
			));
		}
		//get models
		$this->render('index',array(
			'dataProvider'=> $dataProvider,
		));
	}
	public function actionSummary($id=0)
	{

		//criteria
		$criteria = new CDbCriteria;
		$filterSrch     = 0;
		
		
		//channel-name
		$ofilter     = '';
		$filterSrch++;
		$t       = addslashes(trim($id));
		$ofilter = " AND  sked.SchedId = '$t' ";


		$pfilter = "";
		if(Yii::app()->user->AccessType !== "SUPERADMIN")
		{
			$cid     = addslashes(Yii::app()->user->ClientId);
			$pfilter = " AND clnt.ClientId = '$cid' ";
		}

		if(1){
		$rawSql   = "
			SELECT
				sked.* ,
				clnt.CompanyName  ,
				cust.FirstName    ,    
				cust.LastName     ,    
				cust.BirthDate    ,    
				cust.Email        ,
				cust.Gender       ,
				cust.CustomerId  
			FROM
				sched_event_post sked,
				clients   clnt,
				customers cust
			WHERE
				    1=1
			        AND sked.ClientId       = clnt.ClientId
				AND cust.ClientId IN (
					select b.ClientId
					from clients b
					 where
					b.ClientId = clnt.ClientId
				)	
			        $ofilter
			        $pfilter
			";
			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'SchedId',
				    'totalItemCount' => $rawCount,
				    )
			);

		}
		
		if(0)
		{
			echo "
			<hr>
			<pre>
			$rawSql
			</pre>
			<hr>
			";
			exit;
		}
		
		
		//exit;
		$this->render('summary',array(
			'dataProvider' => $dataProvider,
			'model'        => SchedEventPost::model(),
		));

	}


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new SchedEventPost('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['SchedEventPost']))
			$model->attributes=$_GET['SchedEventPost'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return SchedEventPost the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=SchedEventPost::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param SchedEventPost $model the model to be validated
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



    public function actionGetcustomers()
    {

	$ClientId = Yii::app()->request->getParam('ClientId');
        $model = Customers::model()->findAllByAttributes(array('ClientId'=>$ClientId), array('select'=>'CustomerId, FirstName, LastName'));
        $list  = array();

        foreach($model as $row) { $list[$row['CustomerId']] = sprintf("%s %s",$row['LastName'],$row['FirstName']); }
	//give
	Yii::app()->utils->sendJSONResponse($list);
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

		//clientid
		if(Yii::app()->user->AccessType !== "SUPERADMIN") 
		{
			$tid   = addslashes(Yii::app()->user->ClientId);
			$xtra1 = " AND t.ClientId = '$tid' ";
		}
		
		//brand
		$xtra2 = '';
		
		//camp
		$xtra3 = '';

		//chan
		$xtra4 = '';


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

		//clientid
		if(Yii::app()->user->AccessType !== "SUPERADMIN") 
		{
			$tid   = addslashes(Yii::app()->user->ClientId);
			$xtra1 = " AND t.ClientId = '$tid' ";
		}
		
		//brand
		$xtra2 = '';
		
		//camp
		$xtra3 = '';

		//chan
		$xtra4 = '';


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



		//clientid
		if(Yii::app()->user->AccessType !== "SUPERADMIN") 
		{
			$tid   = addslashes(Yii::app()->user->ClientId);
			$xtra1 = " AND t.ClientId = '$tid' ";
		}
		
		//brand
		$xtra2 = '';
		
		//camp
		$xtra3 = '';

		//chan
		$xtra4 = '';


        	$list  = array();

		if(1){
		
			$rawSql = "
				SELECT  t.*
				FROM points t
				WHERE 1=1 
					$xtra1 
					--$xtra2
					--$xtra3
					--$xtra4
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
							$row["Name"]);
		}
		//give
		Yii::app()->utils->sendJSONResponse($list);
	}




	public function actionGetpointlist2()
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
		$xtra2 = '';
		
		//camp
		$xtra3 = '';

		//chan
		$xtra4 = '';


        	$list  = array();

		if(1){
		
			$rawSql = "
				SELECT  t.*,a.PointsAction
				FROM points t,
				     action_type a
				WHERE 1=1  
					AND t.ClientId = a.ClientId
					AND t.PointsId = a.PointsId
					$xtra1 
					$xtra2
					$xtra3
					$xtra4
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'PointsId',
				    'totalItemCount' => $rawCount,
				    )
				);

		}
		
		foreach($dataProvider->getData() as $row)
		{
			$list[$row["PointsId"]] = sprintf("%s - %s",$row["PointsId"],
							$row["PointsAction"]);
		}
		//give
		return $list;
	}

	public function actionGetcouponlist2()
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
		$xtra2 = '';
		
		//camp
		$xtra3 = '';

		//chan
		$xtra4 = '';


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

	public function actionGetRewardlist2()
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
        	$list  = array();

		if(1){
		
			$rawSql = "
			SELECT  t.*
			FROM rewards_list t
			WHERE 1=1 
				$xtra1 
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
