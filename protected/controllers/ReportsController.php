<?php

class ReportsController extends Controller
{
	public $extraJS;
	public $mainDivClass;
	public $modals;
	public $csvRoot;
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
			);
			$criteria->addCondition(" pointlogChannels.ChannelName LIKE '%".addslashes($byChannel)."%' ");
			$criteria->addCondition('t.ClientId = :clientId');
			$criteria->params = array(':clientId' => Yii::app()->user->ClientId);
		}
		//campaign
		$byCampaign   = trim(Yii::app()->request->getParam('byCampaign'));
		if(strlen($byCampaign))
		{
			$filterSrch++;
			$criteria->with = array(
				'pointlogCampaigns' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" pointlogCampaigns.CampaignName LIKE '%".addslashes($byCampaign)."%' ");
			$criteria->addCondition('t.ClientId = :clientId');
			$criteria->params = array(':clientId' => Yii::app()->user->ClientId);
		}
		//brand
		$byBrand   = trim(Yii::app()->request->getParam('byBrand'));
		if(strlen($byBrand))
		{
			$filterSrch++;
			$criteria->with = array(
				'pointlogBrands' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" pointlogBrands.BrandName LIKE '%".addslashes($byBrand)."%' ");
			$criteria->addCondition('t.ClientId = :clientId');
			$criteria->params = array(':clientId' => Yii::app()->user->ClientId);
		}
		//customer
		$byCustomerName   = trim(Yii::app()->request->getParam('byCustomerName'));
		if(strlen($byCustomerName))
		{
			$filterSrch++;
			$criteria->with = array(
				'pointlogCustomers' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" (
						 pointlogCustomers.Email     LIKE '%".addslashes($byCustomerName)."%' OR
						 pointlogCustomers.FirstName LIKE '%".addslashes($byCustomerName)."%' 
						 ) ");
			$criteria->addCondition('t.ClientId = :clientId');
			$criteria->params = array(':clientId' => Yii::app()->user->ClientId);
		}

		
		//no-filter
		if($filterSrch<=0)
		{
			$criteria->scopes = array('thisClient');
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
		
		//exit;
		$this->render('pointsgain',array(
			'dataRes'=>$modRes,
			'dataPts'=>$sumall,
		));
	}

	public function actionCampaignPart()
	{
		$search      = trim(Yii::app()->request->getParam('search'));
		$customer_id = trim(Yii::app()->user->id);
		$criteria = new CDbCriteria;
		$criteria->addCondition('CustomerId = :customer_id');
		$criteria->addCondition("Status     = 'ACTIVE' ");
		$criteria->params = array(':customer_id' => $customer_id);
		
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
	
	public function actionRedeemrewards()
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

	public function actionRedeemcoupons()
	{
		$search      = trim(Yii::app()->request->getParam('search'));
		$customer_id = trim(Yii::app()->user->id);
		$criteria = new CDbCriteria;
		$criteria->addCondition('CustomerId = :customer_id');
		$criteria->params = array(':customer_id' => $customer_id);
		if(strlen($search))
		{
			$criteria->with = array(
				'rewardChannels' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->addCondition(" rewardChannels.ChannelName LIKE '%".addslashes($search)."%' ");
		}
		$dataProvider = new CActiveDataProvider('CouponsRedeem', array(
			'criteria'=> $criteria,
		));


		$this->render('redeemcoupons',array(
			'dataProvider'=>$dataProvider,
		));

	}
	
	public function actionPointsgainbal()
	{
			$search      = trim(Yii::app()->request->getParam('search'));
			$customer_id = trim(Yii::app()->user->id);
			$criteria = new CDbCriteria;
			$criteria->addCondition('CustomerId = :customer_id');
			$criteria->params = array(':customer_id' => $customer_id);
			if(strlen($search))
			{
				$criteria->with = array(
					'rewardChannels' => array('joinType'=>'LEFT JOIN'),
				);
				$criteria->addCondition(" rewardChannels.ChannelName LIKE '%".addslashes($search)."%' ");
			}
			$criteria->with = array(
				'mapBalance' => array('joinType'=>'LEFT JOIN'),
			);
			$criteria->distinct=true;
			$criteria->select='t.ClientId, t.BrandId, t.CampaignId, t.ChannelId';					
			$dataProvider = new CActiveDataProvider('PointsGained', array(
				'criteria'=> $criteria,
			));
	
	
			$this->render('pointsgainbal',array(
				'dataProvider'=>$dataProvider,
			));
	
	}

}
