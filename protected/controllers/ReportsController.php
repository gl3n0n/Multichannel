<?php

class ReportsController extends Controller
{
	public $extraJS;
	public $mainDivClass;
	public $modals;
	public $csvRoot;
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
	 * Lists all models.
	 */
	public function actionIndex()
	{
		
		$this->csvRoot = sprintf("%s/assets/tmf-reports/",Yii::app()->request->baseUrl);
		
		$model=new Reports('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Reports']))
		$model->attributes=$_GET['Reports'];


		//criteria
		$criteria = new CDbCriteria;
		$filterSrch     = 0;
		
		
		//channel-name
		$byChannel   = trim(Yii::app()->request->getParam('byChannel'));
		if(strlen($byChannel))
		{
			$filterSrch++;
			$criteria->with = array(
				'pointlogChannels' => array('joinType'=>'LEFT JOIN'),
				'pointlogCampaigns' => array('joinType'=>'LEFT JOIN'),
				'pointlogBrands'    => array('joinType'=>'LEFT JOIN'),
				'pointlogBrands'    => array('joinType'=>'LEFT JOIN'),
				'pointlogCustomers'    => array('joinType'=>'LEFT JOIN'),

			);
			$criteria->addCondition(" pointlogChannels.ChannelName LIKE '%".addslashes($byChannel)."%' ");
		}
		//campaign
		$byCampaign   = trim(Yii::app()->request->getParam('byCampaign'));
		if(strlen($byCampaign))
		{
			$filterSrch++;
			$criteria->with = array(
				'pointlogChannels' => array('joinType'=>'LEFT JOIN'),
				'pointlogCampaigns' => array('joinType'=>'LEFT JOIN'),
				'pointlogBrands'    => array('joinType'=>'LEFT JOIN'),
				'pointlogBrands'    => array('joinType'=>'LEFT JOIN'),
				'pointlogCustomers'    => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" pointlogCampaigns.CampaignName LIKE '%".addslashes($byCampaign)."%' ");
		}
		//brand
		$byBrand   = trim(Yii::app()->request->getParam('byBrand'));
		if(strlen($byBrand))
		{
			$filterSrch++;
			$criteria->with = array(
				'pointlogChannels' => array('joinType'=>'LEFT JOIN'),
				'pointlogCampaigns' => array('joinType'=>'LEFT JOIN'),
				'pointlogBrands'    => array('joinType'=>'LEFT JOIN'),
				'pointlogBrands'    => array('joinType'=>'LEFT JOIN'),
				'pointlogCustomers'    => array('joinType'=>'LEFT JOIN'),

			);
			$criteria->addCondition(" pointlogBrands.BrandName LIKE '%".addslashes($byBrand)."%' ");
		}
		//customer
		$byCustomerName   = trim(Yii::app()->request->getParam('byCustomerName'));
		if(strlen($byCustomerName))
		{
			$filterSrch++;
			$criteria->with = array(
				'pointlogChannels' => array('joinType'=>'LEFT JOIN'),
				'pointlogCampaigns' => array('joinType'=>'LEFT JOIN'),
				'pointlogBrands'    => array('joinType'=>'LEFT JOIN'),
				'pointlogBrands'    => array('joinType'=>'LEFT JOIN'),
				'pointlogCustomers'    => array('joinType'=>'LEFT JOIN'),

			);
			$criteria->addCondition(" (
						 pointlogCustomers.Email     LIKE '%".addslashes($byCustomerName)."%' OR
						 pointlogCustomers.FirstName LIKE '%".addslashes($byCustomerName)."%' 
						 ) ");
		}

		
		//no-filter
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$criteria->addCondition('t.ClientId = :clientId');
			$criteria->params = array(':clientId' => Yii::app()->user->ClientId);
		}

		$dataProvider = new CActiveDataProvider('Reports', array(
				'criteria'=>$criteria,
				));
		
		//get csv
		$csv = $this->formatCsv($criteria);
		
		//exit;
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
			'downloadCSV'=> (@intval($csv['total'])>0)?($csv['fn']):(''),
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
	
	public function actionCsv()
	{
		$fn   = trim(Yii::app()->request->getParam('fn'));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		header('Content-Description: File Transfer');
		header('Content-Type: application/msexcel');
		header('Content-Disposition: attachment; filename='.basename($csv));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '. filesize($csv));
		@flush();
		readfile($csv);
	}	

	protected function formatCsv($criteria)
	{
		$fn   = sprintf("%s-%s-%s-%s.csv",Yii::app()->params['reportPfx'],@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//get it
		$csvs = new CActiveDataProvider('Reports', array(
			'criteria'=>$criteria,
		));
		
		//set
		$csvs->setPagination(false);
		$total = 0;
		


		//hdr
		$hdr = sprintf('="CUSTOMER NAME",="CUSTOMER EMAIL",="COMPANY NAME",="BRAND NAME",="CAMPAIGN NAME",="CHANNEL NAME",="CREATED BY",="",');
		$this->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		//get csv
		foreach($csvs->getData() as $row) 
		{
		    $total++;
		    //customer
		    $custmail = $row->pointlogCustomers->Email;
		    $custname = sprintf("%s %s",$row->pointlogCustomers->FirstName,$row->pointlogCustomers->LastName );
		    
		    //comp
		    $compname  = $row->pointlogClients->CompanyName;
		    
		    //brand
		    $brandname = $row->pointlogBrands->BrandName;
		    
		    //campaign
		    $cmpgnname = $row->pointlogCampaigns->CampaignName;
		    
		    //channel
		    $chnlname  = $row->pointlogChannels->ChannelName;
		    
		    //by
		    $by        = ($row->pointlogCreateUsers != null)?($row->pointlogCreateUsers->Username):("");
		    
		    //hdr
		    $str = sprintf('="%s",="%s",="%s",="%s",="%s",="%s",="%s",="",',
					$custname,
					$custmail,
					$compname,
					$brandname,
					$cmpgnname,
					$chnlname,
					$by );
		    $this->io_save($csv, str_replace("\n",'', $str)."\n",'a');

		}
		
		
		
		//give it back
		return array(
			'total' => $total,
			'fn'    => $fn
		);
	}
	
	protected function io_save($fname='', $body='', $mode = 'w')
	{
		//mode of fopen
		$mode  = @preg_match("/^(a|append)$/i", $mode) ? ('a') :  ('w');
		
		//open it
		$fh = fopen($fname, $mode);
		if($fh)
		{
			fwrite($fh, $body);
			fclose($fh); 
			$is_ok  = true;
			
		}
		
		//give it back ;-)
		return $is_ok;
		 
	}


	//more reports
	public function actionPointsgain()
	{

		
		$model=new Reports('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Reports']))
		$model->attributes=$_GET['Reports'];


		//criteria
		$criteria   = new CDbCriteria;
		$filterSrch = 0;


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
		

		//get all
		$cid        = Yii::app()->user->id;
		$gdata      = Yii::app()->db->createCommand()
				->select('CS.CustomerId, CS.ClientId, CS.BrandId, CS.CampaignId, CS.ChannelId, CP.Balance')
				->from('Customer_Subscriptions CS, 
					Customer_Points CP')
				->where('CS.CustomerId =:vId 
						AND CS.Status =:vStatus 
						AND CP.SubscriptionId = CS.SubscriptionId',
					array(':vId'=>$cid,':vStatus'=> 'ACTIVE'))
				->group('CS.ClientId, CS.BrandId, CS.CampaignId, CS.ChannelId')			
				->queryAll();
		$modRes     = array();
		$sumall     = 0;
		$total      = 0;
		foreach($gdata as $row)
		{
			$total++;
			$modRes[] = array(
				'CLIENTS'    => $clients[$row['ClientId']],
				'BRANDS'     => $brands[$row['BrandId']],
				'CAMPAIGNS'  => $campaigns[$row['CampaignId']],
				'CHANNELS'   => $channels[$row['ChannelId']],
				'BALANCE'    => $row['Balance'],
			);
			$sumall  += $row['Balance'];
			
		}
		
		if(0){
			echo "<hr> $sumall#CustomerSubscriptions<hr>".@var_export($modRes,true);			 		
			exit;
		}
	
		//$dataSet=new CActiveDataProvider('CustomerSubscriptions',array('data'=> $modRes));
		$dataProvider = new CActiveDataProvider('CustomerSubscriptions', array(
					'criteria'=> $criteria,
		));
		$dataProvider->setData($modRes);
		//exit;
		$this->render('pointsgain',array(
			'dataRes'=>$modRes,
			'dataPts'=>$sumall,
			'dataProvider'=>$dataProvider,
		));
	}

	public function actionCampaignPartXXXX()
	{
		$search      = trim(Yii::app()->request->getParam('search'));
		$customer_id = trim(Yii::app()->user->id);
		$criteria = new CDbCriteria;
		
		$criteria->addCondition(" t.Status     = 'ACTIVE' ");
		
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$clid   = addslashes(Yii::app()->user->ClientId);
			$criteria->addCondition(" t.ClientId = '$clid' ");
		}
		
		if(strlen($search))
		{
			$criteria->with = array(
				'subsChannels' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" subsChannels.ChannelName LIKE '%".addslashes($search)."%' ");
		}
		$dataProvider = new CActiveDataProvider('CustomerSubscriptions', array(
			'criteria'=> $criteria,
		));


		$this->render('campaignpart',array(
			'dataProvider'=>$dataProvider,
		));

	}
	
	public function actionRedeemrewardsXXXX()
	{
		$search      = trim(Yii::app()->request->getParam('search'));
		$customer_id = trim(Yii::app()->user->id);
		$criteria = new CDbCriteria;
		$criteria->addCondition('UserId = :customer_id');
		$criteria->params = array(':customer_id' => $customer_id);
		if(strlen($search))
		{
			$criteria->with = array(
				'rewardChannels' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" rewardChannels.ChannelName LIKE '%".addslashes($search)."%' ");
		}
		$dataProvider = new CActiveDataProvider('RewardsRedeem', array(
			'criteria'=> $criteria,
		));


		$this->render('redeemrewards',array(
			'dataProvider'=>$dataProvider,
		));

	}

	
	public function actionPointsgainbal()
	{
			$search      = trim(Yii::app()->request->getParam('search'));
			$customer_id = trim(Yii::app()->user->id);
			$criteria    = new CDbCriteria;
			$xfilter      = '';
			$yfilter      = '';
			if(strlen($search))
			{
			   $srch    = addslashes($search);
			   $xfilter =  " AND e.ChannelName LIKE '%$srch%'  ";
			}
			
			if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
			{
				$clid   = addslashes(Yii::app()->user->ClientId);
				$yfilter= " AND g.ClientId = '$clid' ";
			}


			if(1){
			$rawSql   = "
			SELECT a.CustomerPointId, 
			       b.SubscriptionId, 
			       a.Balance, 
			       a.Used, 
			       a.Total, 
			       b.CustomerId, 
			       b.BrandId, 
			       b.CampaignId, 
			       b.ChannelId, 
			       b.Status, 
			       f.CampaignName, 
			       g.CompanyName, 
			       d.BrandName, 
			       e.ChannelName
			FROM customer_points a
				join customer_subscriptions b on a.SubscriptionId = b.SubscriptionId 
				join brands d on b.BrandId       = d.BrandId
				join channels e on b.ChannelId   = e.ChannelId
				join campaigns f on b.CampaignId = f.CampaignId
				join clients g on b.ClientId     = g.ClientId
			WHERE b.Status = 'ACTIVE'
			$xfilter
			$yfilter
			";
			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'CustomerPointId',
				    'totalItemCount' => $rawCount,
				    )
			);

			}
			
			

			$mapping =  $this->getMoreLists();

			$this->render('pointsgainbal',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,


			));
	
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
/**
	 * Manages all models.
	 */
	public function actionRedeemcoupons()
	{
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		//all-pending
		
		if(empty($uid))
			$uid  = @addslashes(trim(Yii::app()->request->getParam('uid')));
		
		
		$clid   = addslashes(Yii::app()->user->ClientId);
		$xtra   = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$xtra   = " AND coupon_mapping.ClientId = '$clid'  ";
		}
		$filter = '';
		if(strlen($search)) 
		    $filter = " AND generated_coupons.Code LIKE '%".addslashes($search)."%' ";
		
		if(1){
		$rawSql   = "
				SELECT 
					FirstName, 
					MiddleName, LastName, Email,BrandName, 
					generated_coupons.GeneratedCouponId, 
					generated_coupons.CustomerId as CustomerId, 
					generated_coupons.CouponId as CouponId, 
					generated_coupons.Code as Code, 
					coupon.Type, TypeId, Source, ExpiryDate, 
					coupon.Status, coupon_mapping.ClientId, 
					coupon_mapping.BrandId, 
					coupon_mapping.ChannelId, 
					coupon_mapping.CampaignId, 
					campaigns.CampaignName as CampaignName, 
					channels.ChannelName as ChannelName, 
					DateRedeemed 
				FROM 
				coupon join generated_coupons on coupon.CouponId = generated_coupons.CouponId 
				       join coupon_mapping on coupon_mapping.CouponMappingId = generated_coupons.CouponMappingId 
				       join brands on coupon_mapping.BrandId = brands.BrandId 
				       join customers on customers.CustomerId = generated_coupons.CustomerId 
				       join campaigns on campaigns.CampaignId = coupon_mapping.CampaignId 
				       join channels on channels.ChannelId = coupon_mapping.ChannelId
				WHERE 1=1
				AND generated_coupons.Status IN ('REDEEMED')
				$xtra
				$filter
		";
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProvider    = new CSqlDataProvider($rawData, array(
					    'keyField' => 'GeneratedCouponId',
					    'totalItemCount' => $rawCount,
					    )
			);
		
		}
    		if(0){
    		
    		//echo '<hr><hr>'.@var_export($criteria,true);
    		//echo '<hr><hr>'.@var_export($dataProvider,true);
    		foreach($dataProvider->getData() as $row)
    		{
    			echo '<hr><hr>'.@var_export($row,true);
    		}
    		
    		echo '<hr><hr>'.@var_export($brands,true);
    		echo '<hr><hr>'.@var_export($campaigns,true);
    		echo '<hr><hr>'.@var_export($clients,true);
    		echo '<hr><hr>'.@var_export($channels,true);
    		exit;
    		}
    		
    		
		$mapping =  $this->getMoreLists();
		
		$this->render('redeemcoupons',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
			
			
		));
	}
	 
	public function actionCustomeractivity($customer_id=0)
	{
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		//all-pending
		
		if(empty($uid))
			$uid  = @addslashes(trim(Yii::app()->request->getParam('uid')));
		
		
		$clid   = addslashes(Yii::app()->user->ClientId);
		$xtra   = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$xtra   = " AND a.ClientId = '$clid'  ";
		}
		
		$vcust  = trim(Yii::app()->request->getParam('customer_id'));
		if($vcust>0) $customer_id = $vcust;
		
		$vlid   = addslashes($customer_id);
		$vxtra  = '';
		if($customer_id > 0)
		{
			$vxtra   = " AND a.CustomerId = '$vlid'  ";
		}
		$filter = '';
		if(strlen($search) > 0) 
		{
			$srch   = addslashes($search);
			$filter = " AND  (
						e.Email     LIKE '%$srch%'  OR 
						e.FirstName LIKE '%$srch%'  OR 
						e.LastName  LIKE '%$srch%'   
			                 ) ";
		}
		    
		
		if(1){
		$rawSql   = " /**
				select
				      a.CustomerId, 
				      e.Email,
				      e.FirstName,
				      e.LastName,
				      a.SubscriptionId, 
				      a.ClientId, a.BrandId, a.CampaignId, a.ChannelId, 
				      f.CompanyName, g.BrandName, h.CampaignName, i.ChannelName, 
				      a.status SubsriptionStatus,
				      b.Balance, 
				      b.Used, 
				      b.Total,
				      c.PointsId, 
				      d.Value Points
				from  customer_subscriptions a, 
				      customer_points b, 
				      points_log c, 
				      points d,
				      customers e, 
				      clients f,
				      brands g,
				      campaigns h,
				      channels i
				where a.SubscriptionId = b.SubscriptionId
				and   a.SubscriptionId = c.SubscriptionId
				and   a.CustomerId     = c.CustomerId
				and   a.CustomerId     = e.CustomerId
				and   a.ClientId       = f.ClientId
				and   a.BrandId        = g.ClientId
				and   a.CampaignId     = h.CampaignId
				and   a.ChannelId      = i.ChannelId
				and   c.PointsId       = d.PointsId $xtra $vxtra $filter
				union all
				select  a.CustomerId, 
				        e.Email,
				        e.FirstName,
				        e.LastName,
					a.SubscriptionId, 
					a.ClientId, a.BrandId, a.CampaignId, a.ChannelId, 
					f.CompanyName, g.BrandName, h.CampaignName, i.ChannelName, 
					a.status SubsriptionStatus,
				        b.Balance, b.Used, b.Total,
				       ifnull(c.PointsId,0), c.Points Points
				from  customer_subscriptions a, 
				      customer_points b, 
				      points_log c, 
				      customers e,clients f,brands g,campaigns h,channels i
				where a.SubscriptionId = b.SubscriptionId
				and   a.SubscriptionId = c.SubscriptionId
				and   a.CustomerId     = c.CustomerId
				and   a.CustomerId     = e.CustomerId
				and   a.ClientId       = f.ClientId
				and   a.BrandId        = g.BrandId
				and   a.CampaignId     = h.CampaignId
				and   a.ChannelId      = i.ChannelId
				and   (c.PointsId      = 0 or c.PointsId is null)**/";
				
			$rawSql = "
				select a.CustomerId, 
				       a.SubscriptionId, 
				       a.ClientId, 
				       a.BrandId, 
				       a.CampaignId, 
				       a.ChannelId, 
				       a.status SubsriptionStatus,
				       b.Balance, 
				       b.Used, 
				       b.Total,
				       e.Email,
				       e.FirstName,
				       e.LastName,
				       f.CompanyName, g.BrandName, h.CampaignName, i.ChannelName
				from  customer_subscriptions a, 
				      customer_points b,
				      customers e,clients f,brands g,campaigns h,channels i
				where 1=1
				and   a.SubscriptionId = b.SubscriptionId			
				and   a.CustomerId     = e.CustomerId
				and   a.ClientId       = f.ClientId
				and   a.BrandId        = g.ClientId
				and   a.CampaignId     = h.CampaignId
				and   a.ChannelId      = i.ChannelId
				$xtra
				$vxtra
				$filter
				";
		
		
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProvider    = new CSqlDataProvider($rawData, array(
					    'keyField' => 'SubscriptionId',
					    'totalItemCount' => $rawCount,
					    )
			);
		
		}
		if(0){
    		
    		//echo '<hr><hr>'.@var_export($criteria,true);
    		//echo '<hr><hr>'.@var_export($dataProvider,true);
    		foreach($dataProvider->getData() as $row)
    		{
    			echo '<hr><hr>'.@var_export($row,true);
    		}
    		
    		echo '<hr><hr>'.@var_export($brands,true);
    		echo '<hr><hr>'.@var_export($campaigns,true);
    		echo '<hr><hr>'.@var_export($clients,true);
    		echo '<hr><hr>'.@var_export($channels,true);
    		exit;
    		}
    		
    		
		$mapping =  $this->getMoreLists();
		
		$this->render('customeractivity',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
			
			
		));
	}


	public function actionSubcriptionsum($subscribid=0)
	{
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		//all-pending
		
		if(empty($uid))
			$uid  = @addslashes(trim(Yii::app()->request->getParam('uid')));
		
		
		$clid   = addslashes(Yii::app()->user->ClientId);
		$xtra   = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$xtra   = " AND a.ClientId = '$clid'  ";
		}
		
		$subs  = trim(Yii::app()->request->getParam('subscribid'));
		if($subs>0) $subscribid = $subs;
		
		$vlid   = addslashes($subscribid);
		$vxtra  = " AND a.SubscriptionId = '$vlid'  ";
		$filter = '';
		if(strlen($search) > 0) 
		{
			$srch   = addslashes($search);
			$filter = " AND  (
						i.ChannelName  LIKE '%$srch%'   
			                 ) ";
		}
		    
		
		if(1){
			$rawSql = "
				select a.PointLogId,
				       a.CustomerId, 
				       a.SubscriptionId, 
				       a.ClientId, 
				       a.BrandId, 
				       a.CampaignId, 
				       a.ChannelId, 
				       a.PointsId, 
				       b.Value Points,
				       f.CompanyName, g.BrandName, h.CampaignName, i.ChannelName
				from  points_log a, 
				      points b,
				      customers e,clients f,brands g,campaigns h,channels i
				where   a.PointsId = b.PointsId
					and   a.CustomerId     = e.CustomerId
					and   a.ClientId       = f.ClientId
					and   a.BrandId        = g.ClientId
					and   a.CampaignId     = h.CampaignId
					and   a.ChannelId      = i.ChannelId $xtra $vxtra $filter
				union all
					select a.PointLogId,
					       a.CustomerId, 
					       a.SubscriptionId, 
					       a.ClientId, 
					       a.BrandId, 
					       a.CampaignId, 
					       a.ChannelId, 
					       ifnull(a.PointsId,0), a.Points Points,
					       f.CompanyName, g.BrandName, h.CampaignName, i.ChannelName
					from  points_log a,
					      customers e,clients f,brands g,campaigns h,channels i
					where ( a.PointsId = 0 or a.PointsId is null )
					and   a.CustomerId     = e.CustomerId
					and   a.ClientId       = f.ClientId
					and   a.BrandId        = g.BrandId
					and   a.CampaignId     = h.CampaignId
					and   a.ChannelId      = i.ChannelId
					$xtra $vxtra $filter
				";
		
		
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProvider    = new CSqlDataProvider($rawData, array(
					    'keyField' => 'SubscriptionId',
					    'totalItemCount' => $rawCount,
					    )
			);
		
		}
		if(0){
    		
    		//echo '<hr><hr>'.@var_export($criteria,true);
    		//echo '<hr><hr>'.@var_export($dataProvider,true);
    		foreach($dataProvider->getData() as $row)
    		{
    			echo '<hr><hr>'.@var_export($row,true);
    		}
    		
    		echo '<hr><hr>'.@var_export($brands,true);
    		echo '<hr><hr>'.@var_export($campaigns,true);
    		echo '<hr><hr>'.@var_export($clients,true);
    		echo '<hr><hr>'.@var_export($channels,true);
    		exit;
    		}
    		
    		
		$mapping =  $this->getMoreLists();
		
		$this->render('subcriptionsum',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
			
			
		));
	}	
	
	
	public function actionRedeemrewards()
	{
			$search   = trim(Yii::app()->request->getParam('search'));
			$criteria = new CDbCriteria;
			//all-pending
			
			if(empty($uid))
				$uid  = @addslashes(trim(Yii::app()->request->getParam('uid')));
			
			
			$clid   = addslashes(Yii::app()->user->ClientId);
			$xtra   = '';
			if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
			{
				$xtra   = " AND a.ClientId = '$clid'  ";
			}
			
			$filter = '';
			if(strlen($search) > 0) 
			{
				$srch   = addslashes($search);
				$filter = " AND  (
							f.ChannelName  LIKE '%$srch%'   
				                 ) ";
			}
			    
			
			if(1){
				$rawSql = "
					select a.RedeemedId, 
					       a.RewardId,    
					       b.Title,    
					       b.Description,    
					       b.Value,    
					       a.UserId,      
					       a.Source,      
					       a.Action,      
					       a.ClientId,    
					       a.BrandId,     
					       a.CampaignId,  
					       a.ChannelId,   
					       a.DateRedeemed, 
					       c.CompanyName,
					       d.BrandName, 
					       e.CampaignName,
					       f.ChannelName
					from redeemed_reward a
					join rewards_list b on b.RewardId = a.RewardId
					join clients c on c.ClientId     = a.ClientId
					join brands d on d.BrandId       = a.BrandId
					join campaigns e on e.CampaignId = a.CampaignId
					join channels f on f.ChannelId   = a.ChannelId
					WHERE 1=1
					$xtra  $filter
					";
			
			
			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
						    'keyField' => 'RedeemedId',
						    'totalItemCount' => $rawCount,
						    )
				);
			
			}
			if(0){
	    		
	    		//echo '<hr><hr>'.@var_export($criteria,true);
	    		//echo '<hr><hr>'.@var_export($dataProvider,true);
	    		foreach($dataProvider->getData() as $row)
	    		{
	    			echo '<hr><hr>'.@var_export($row,true);
	    		}
	    		
	    		echo '<hr><hr>'.@var_export($brands,true);
	    		echo '<hr><hr>'.@var_export($campaigns,true);
	    		echo '<hr><hr>'.@var_export($clients,true);
	    		echo '<hr><hr>'.@var_export($channels,true);
	    		exit;
	    		}
	    		
	    		
			$mapping =  $this->getMoreLists();
			
			$this->render('redeemrewards',array(
				'dataProvider' => $dataProvider,
				'mapping'      => $mapping,
				
				
			));
	}	
	
	public function actionCampaignPart()
	{
			$search   = trim(Yii::app()->request->getParam('search'));
			$criteria = new CDbCriteria;
			//all-pending
			
			if(empty($uid))
				$uid  = @addslashes(trim(Yii::app()->request->getParam('uid')));
			
			
			$clid   = addslashes(Yii::app()->user->ClientId);
			$xtra   = '';
			if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
			{
				$xtra   = " AND g.ClientId = '$clid'  ";
			}
			
			$filter = '';
			if(strlen($search) > 0) 
			{
				$srch   = addslashes($search);
				$filter = " AND  (
							e.ChannelName  LIKE '%$srch%'   
				                 ) ";
			}
			    
			
			if(1){
				$rawSql = "

					SELECT b.CustomerId, 
					      b.BrandId, 
					      b.CampaignId, 
					      f.CampaignName, 
					      g.CompanyName, 
					      d.BrandName,
					      e.ChannelName,
					      '' as Description,
					      b.Status
					FROM customer_subscriptions b
					join brands d on b.BrandId       = d.BrandId
					join channels e on b.ChannelId   = e.ChannelId
					join campaigns f on b.CampaignId = f.CampaignId
					join clients g on b.ClientId     = g.ClientId
					WHERE 1=1 AND b.status='ACTIVE' $xtra  $filter
					group by b.CustomerId, 
					      b.BrandId, 
					      b.CampaignId, 
					      b.Status, 
					      f.CampaignName, 
					      g.CompanyName, 
					      d.BrandName					
					
					";
			
			
			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
						    'keyField' => 'CustomerId',
						    'totalItemCount' => $rawCount,
						    )
				);
			
			}
			if(0){
	    		
	    		//echo '<hr><hr>'.@var_export($criteria,true);
	    		//echo '<hr><hr>'.@var_export($dataProvider,true);
	    		foreach($dataProvider->getData() as $row)
	    		{
	    			echo '<hr><hr>'.@var_export($row,true);
	    		}
	    		
	    		echo '<hr><hr>'.@var_export($brands,true);
	    		echo '<hr><hr>'.@var_export($campaigns,true);
	    		echo '<hr><hr>'.@var_export($clients,true);
	    		echo '<hr><hr>'.@var_export($channels,true);
	    		exit;
	    		}
	    		
	    		
			$mapping =  $this->getMoreLists();
			
			$this->render('campaignpart',array(
				'dataProvider' => $dataProvider,
				'mapping'      => $mapping,
				
				
			));
	}		
}
