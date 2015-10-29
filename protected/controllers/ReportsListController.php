<?php

class ReportsListController extends Controller
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
		$ofilter     = '';
		if(strlen($byChannel))
		{
			$filterSrch++;
			$ofilter = " AND chan.ChannelName LIKE '%".addslashes($byChannel)."%' ";

		}
		//campaign
		$byCampaign   = trim(Yii::app()->request->getParam('byCampaign'));
		$pfilter     = '';
		if(strlen($byCampaign))
		{
			$filterSrch++;
			$pfilter = " AND camp.CampaignName LIKE '%".addslashes($byCampaign)."%' ";
		}
		//brand
		$byBrand   = trim(Yii::app()->request->getParam('byBrand'));
		$qfilter     = '';
		if(strlen($byBrand))
		{
			$filterSrch++;
			$qfilter = " AND brnd.BrandName LIKE '%".addslashes($byBrand)."%' ";
		}
		//customer
		$byClientName = trim(Yii::app()->request->getParam('byClientName'));
		$rfilter      = '';
		if(strlen($byClientName))
		{
			$filterSrch++;
			$rfilter = " AND (
						 clnt.CompanyName LIKE '%".addslashes($byClientName)."%'
				     ) ";
		}
		//date: 
		$byTranDateFr = trim(Yii::app()->request->getParam('byTranDateFr'));
		$dtfilter1     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateFr))
		{
			$filterSrch++;
			$t = addslashes($byTranDateFr);
			$dtfilter1 = " AND ( ptslog.DateCreated >= '$t 00:00:00' ) ";
		}
		//date: 
		$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
		$dtfilter2     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
		{
			$filterSrch++;
			$t = addslashes($byTranDateTo);
			$dtfilter2 = " AND ( ptslog.DateCreated <= '$t 23:59:59' ) ";
		}
				
		
		//no-filter
		$sfilter     = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$qid     =  addslashes(Yii::app()->user->ClientId);
			$sfilter = " AND ptslog.ClientId = '$qid' ";
		}


		//email + cust-name + etc
		if('sortby' == 'sortby')
		{
				// set sort options
				$sort = new CSort;
				$sort->attributes = array(
								'*',
								'EmailAdd'       => array(
										'asc'    =>'cust.Email',
										'desc'   =>'cust.Email DESC',
										'label'  =>'Email',
								),
								'CustomerId'     => array(
									'asc'    =>'cust.CustomerId',
									'desc'   =>'cust.CustomerId DESC',
									'label'  =>'CustomerId',
								),										
								'CustomerNm'  => array(
										'asc'    =>'cust.LastName',
										'desc'   =>'cust.LastName DESC',
										'label'  =>'Customer Name',
								),
								'PointsName'  => array(
										'asc'    =>'pts.Name',
										'desc'   =>'pts.Name DESC',
										'label'  =>'PointsName',
								),
								'Last Transaction'  => array(
										'asc'    =>'pts.DateCreated',
										'desc'   =>'pts.DateCreated DESC',
										'label'  =>'Last Transaction',
								),
								'Client'  => array(
										'asc'    =>'clnt.CompanyName',
										'desc'   =>'clnt.CompanyName DESC',
										'label'  =>'Client',
								),
								'Brand'  => array(
										'asc'    =>'brnd.BrandName',
										'desc'   =>'brnd.BrandName DESC',
										'label'  =>'Brand',
								),
								'Campaign'  => array(
										'asc'    =>'camp.CampaignName',
										'desc'   =>'camp.CampaignName DESC',
										'label'  =>'Campaign',
								),
								'Channel'  => array(
										'asc'    =>'chan.ChannelName',
										'desc'   =>'chan.ChannelName DESC',
										'label'  =>'Channel',
								),

								
								
								
								
								
				);
				//$sort->multiSort  = true;
		}


		$dataProvider = new CActiveDataProvider('Reports', array(
				'criteria'=> $criteria,
				
				));


		if(1){
		$rawSql   = "
				SELECT 
					ptslog.ClientId      ,
					ptslog.CustomerId    ,
					ptslog.PointLogId    ,
					ptslog.ChannelId     ,
					chan.ChannelName     ,
					(
						select brnd.BrandName
						from
						brands brnd
						where
						  brnd.BrandId = ptslog.BrandId
						limit 1 
					) as BrandName ,
					(
						select camp.CampaignName
						from
						campaigns camp
						where
						  camp.CampaignId = ptslog.CampaignId
						limit 1 
					) as CampaignName ,
					clnt.CompanyName,
					ptslog.PointsId       ,
					pts.Name as PointsName,
					act.Name as ActionTypeName,
					act.Value as ActionTypeValue,
					cust.BirthDate        ,
					cust.FirstName        ,
					cust.LastName         ,
					cust.Email            ,					
					MAX(ptslog.DateCreated) DateCreated
				FROM 
					  points_log ptslog,
					  customers cust,
					  clients clnt,
					  points pts,
					  action_type act,
					  channels chan,
					  brands brnd,
					  campaigns camp
				WHERE 1=1
					  AND   ptslog.CustomerId     = cust.CustomerId
					  AND   ptslog.ClientId       = clnt.ClientId
					  AND   ptslog.ActiontypeId   = act.ActiontypeId
					  AND   ptslog.PointsId       = pts.PointsId
					  AND   pts.PointsId          = act.PointsId
					  AND   ptslog.ChannelId      = chan.ChannelId
					  AND   ptslog.BrandId        = brnd.BrandId
					  AND   ptslog.CampaignId     = camp.CampaignId
						$ofilter 
						$pfilter 
						$qfilter 
						$rfilter 
						$sfilter 
						$dtfilter1
						$dtfilter2
			GROUP BY 
					ptslog.CustomerId ,
					PointsName
			ORDER BY DateCreated DESC
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'PointLogId',
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
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
		
		//get csv
		$csv = $this->formatCsv($rawSql,$criteria,$sort);
		
		//exit;
		$this->render('index',array(
			'dataProvider' => $dataProvider,
			'model'        => $model,
			'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),
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
		$model = ReportsList::model()->findByPk($id);
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
		
		//download
		$utils= new Utils;
		$utils->push_xls_download($csv);
		
	}	

	protected function formatCsv($rawSql, $criteria, $sort)
	{
		$fn   = sprintf("%s-%s-%s-%s.csv",Yii::app()->params['reportPfx'],@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//ensure
		if (!@file_exists(Yii::app()->params['reportCsv'])) {
		    @mkdir(Yii::app()->params['reportCsv'], 0777, true);
		}
		
		//fmt it her		
		$rawData       = Yii::app()->db->createCommand($rawSql); 
		$rawCount      = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dbprovider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'PointLogId',
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
		);

		//set
		$dbprovider->setPagination(false);
		$total = 0;
		
		//hdr
		$hdr_ttl = array("SEQ. NO.",
				 "CUSTOMER ID",
				 "CUSTOMER NAME",
				 "CUSTOMER EMAIL",
				 "CUSTOMER BDAY",
				 "COMPANY NAME",
				 "BRAND NAME",
				 "POINTS NAME",
				 "CAMPAIGN NAME",
				 "CHANNEL NAME",
				 "DATE CREATED",
			 );

		$utils = new Utils;
		$hdr   = $utils->fmt_csv($hdr_ttl);
		
		$utils->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		$total = 0;
		//get csv
		foreach($dbprovider->getData() as $row) 
		{
			
			$total++;
			//customer
			$custmail = $row["Email"];
			$custuid  = $row["CustomerId"];
			$custbday = $row["BirthDate"];
			$custname = sprintf("%s %s",$row["FirstName"],$row["LastName"] );

			//comp
			$compname  = $row["CompanyName"];

			//brand
			$brandname = $row["BrandName"];

			//campaign
			$ptsname       = $row["PointsName"];
			$ChannelName   = $row["ChannelName"];
			$CampaignName  = $row["CampaignName"];


			//fmt
			$udata   = array();
			$udata[] = trim($total     );
			$udata[] = trim($custuid   );
			$udata[] = trim($custname  );
			$udata[] = trim($custmail  );
			$udata[] = trim($custbday  );
			$udata[] = trim($compname  );
			$udata[] = trim($brandname );
			$udata[] = trim($ptsname );
			$udata[] = trim($CampaignName    );
			$udata[] = trim($ChannelName     );   
			$udata[] = trim($row["DateCreated"] );  
			//fmt
			$str   = $utils->fmt_csv($udata);

			$utils->io_save($csv, str_replace("\n",'', $str)."\n",'a');
		}
		
		//give it back
		return array(
			'total' => $total,
			'fn'    => $fn
		);
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
				'select'=>'BrandId, BrandName', 'condition' =>" status='ACTIVE '"));
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
			      h.Email, 
			      b.SubscriptionId, 
			      a.Balance, 
			      a.Used, 
			      a.Total, 
			      b.CustomerId, 
			      b.BrandId, 
			      b.CampaignId, 
			      b.Status, 
			      f.CampaignName, 
			      g.CompanyName, 
			      d.BrandName, 
			      (
				   select chan.ChannelName
				   from channels chan
				   where
					chan.ClientId    = b.ClientId
					and
					chan.BrandId     = b.BrandId
					and
					chan.CampaignId  = b.CampaignId
				   limit 1
			       ) as ChannelName,
			      (
				   select chan.ChannelId
				   from channels chan
				   where
					chan.ClientId    = b.ClientId
					and
					chan.BrandId     = b.BrandId
					and
					chan.CampaignId  = b.CampaignId
				   limit 1
			       ) as ChannelId
			FROM customer_points a,
			     customer_subscriptions b,
			     brands d,
			     campaigns f,
			     clients g,
			     customers h
			WHERE   1=1
				AND b.Status         = 'ACTIVE' 
				AND a.SubscriptionId = b.SubscriptionId 
				AND b.BrandId      = d.BrandId
				AND b.CampaignId   = f.CampaignId
				AND b.ClientId     = g.ClientId
				AND b.CustomerId   = h.CustomerId
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

			$csv = $this->formatCsvPointsgainbal($rawSql,null,null);

			$this->render('pointsgainbal',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
			'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),
			));
	
	}

	protected function formatCsvPointsgainbal($rawSql, $criteria, $sort)
	{
		$fn   = sprintf("%s-Pointsgainbal-%s-%s-%s.csv",Yii::app()->params['reportPfx'],@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//ensure
		if (!@file_exists(Yii::app()->params['reportCsv'])) {
		    @mkdir(Yii::app()->params['reportCsv'], 0777, true);
		}
		
		//fmt it her		
		$rawData       = Yii::app()->db->createCommand($rawSql); 
		$rawCount      = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dbprovider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'SubscriptionId',
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
		);

		//set
		$dbprovider->setPagination(false);
		$total = 0;
		
		//hdr
		$hdr_ttl = array("SEQ. NO.",
				 "CUSTOMER ID",
				 "CUSTOMER EMAIL",
				 "POINTS",
				 "CLIENT",
				 "BRAND",
				 "CAMPAIGN",
				 "CHANNEL",
			 );

		$utils = new Utils;
		$hdr   = $utils->fmt_csv($hdr_ttl);
		
		$utils->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		$total  = 0;
		$sumall = 0;
		//get csv
		foreach($dbprovider->getData() as $row) 
		{
			
			$total++;
			//customer

			//fmt
			$udata   = array();
			$udata[] = trim($total     );
			$udata[] = trim($row["CustomerId"]);
			$udata[] = trim($row["Email"]);
			$udata[] = trim($row["Balance"]);
			$udata[] = trim($row["CompanyName"]);
			$udata[] = trim($row["BrandName"]);
			$udata[] = trim($row["CampaignName"]);
			$udata[] = trim($row["ChannelName"]);
			
			$sumall += @intval($row["Total"]);
			//fmt
			$str   = $utils->fmt_csv($udata);

			$utils->io_save($csv, str_replace("\n",'', $str)."\n",'a');
		}
		
		$udata   = array();
		$udata[] = trim("Current Total Points:");
		$udata[] = trim($sumall);
		//fmt
		$str   = $utils->fmt_csv($udata);

		$utils->io_save($csv, str_replace("\n",'', $str)."\n",'a');

		//give it back
		return array(
			'total' => $total,
			'fn'    => $fn
		);
	}


	public function getMoreLists()
	{

		$yfilter='';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$cid    = addslashes(Yii::app()->user->ClientId);
			$yfilter= " AND ClientId = '$cid' ";
		}

		//brands		
		$_brands = Brands::model()->findAll(array(
				'select'=>'BrandId, BrandName', 'condition'=>" status='ACTIVE' $yfilter"));
		$brands = CHtml::listData($_brands, 'BrandId', 'BrandName');

		//campaigns
		$_campaigns = Campaigns::model()->findAll(array(
			     'select'=>'CampaignId, CampaignName', 'condition'=>" status='ACTIVE' $yfilter"));
		$campaigns  = CHtml::listData($_campaigns, 'CampaignId', 'CampaignName');

		//clients		
		$_clients   = Clients::model()->findAll(array(
				'select'=>'ClientId, CompanyName', 'condition'=>" status='ACTIVE' $yfilter"));
		$clients    = CHtml::listData($_clients, 'ClientId',  'CompanyName');

		//channels
		$_channels   = Channels::model()->findAll(array(
				'select'=>'ChannelId, ChannelName', 'condition'=>" status='ACTIVE' $yfilter"));
		$channels    = CHtml::listData($_channels, 'ChannelId',  'ChannelName');

		$_channels   = Channels::model()->findAll(array(
				'select'=>'ChannelId, ChannelName', 'condition'=>" status='ACTIVE' $yfilter"));
		$channels    = CHtml::listData($_channels, 'ChannelId',  'ChannelName');


		//customers
		$clid   = addslashes(Yii::app()->user->ClientId);
		$cand   = (Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  ? (" AND ClientId='$clid' ") : ('');
		$_customers = Customers::model()->findAll(array(
				'select'=>'CustomerId, Email', 'condition'=> " status = 'ACTIVE' $cand "));
		$customers  = CHtml::listData($_customers, 'CustomerId', 'Email');
		
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
			$xtra   = " AND map.ClientId = '$clid'  ";
		}

		$filter     = '';
		$filterSrch = 0;

		//points-name
		$byPointsName   = trim(Yii::app()->request->getParam('byPointsName'));
		$filterX1       = '';
		if(strlen($byPointsName))
		{
			$filterSrch++;
			$t = addslashes($byPointsName);
			$filterX1  = " AND EXISTS (
									SELECT 1
									From points pts
									Where 
										pts.PointsId = gen.PointsId
										AND pts.Name LIKE '%$t%'
									LIMIT 1
							) ";
		}

		//customer-name,
		$byCustomer   = trim(Yii::app()->request->getParam('byCustomer'));
		$filterX2       = '';
		if(strlen($byCustomer))
		{
			$filterSrch++;
			$t = addslashes($byCustomer);
			$filterX2  = " AND EXISTS (
									SELECT 1
								   from
									customers  custm
								   where
									custm.CustomerId = gen.CustomerId
									AND (
										custm.FirstName LIKE '%$t%'
										or 
										custm.LastName  LIKE '%$t%'
									)
									LIMIT 1
						) ";
		}
		//coupontype
		$byCouponType   = trim(Yii::app()->request->getParam('byCouponType'));
		$filterX3       = '';
		if(strlen($byCouponType))
		{
			$filterSrch++;
			$t = addslashes($byCouponType);
			$filterX3  = " AND ( map.CouponType = '$t' ) ";
		}
		
		//date: 
		$byTranDateFr = trim(Yii::app()->request->getParam('byTranDateFr'));
		$dtfilter1     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateFr))
		{
			$filterSrch++;
			$t = addslashes($byTranDateFr);
			$dtfilter1 = " AND ( gen.DateRedeemed >= '$t 00:00:00' ) ";
		}
		//date: 
		$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
		$dtfilter2     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
		{
			$filterSrch++;
			$t = addslashes($byTranDateTo);
			$dtfilter2 = " AND ( gen.DateRedeemed <= '$t 23:59:59' ) ";
		}
				
		//by client
		$dtfilter3 = '';
		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN' and isset($_REQUEST['Clients'])) 
		{
			$byClient = $_REQUEST['Clients']['ClientId'];
			if($byClient>0)
			{
				$t = addslashes($byClient);
				$dtfilter3 = " AND map.ClientId = '$t' ";
			}			
		}


		
		if(1){
		$rawSql   = "
			SELECT  DISTINCT 
				gen.GeneratedCouponId,
				gen.Code,
			    gen.PointsId,
				(
					select pts.Name
					from
					 points pts
					where
					pts.PointsId = gen.PointsId
					limit 1	
				) as PointsSystemName,
				gen.CustomerId,
				(
				   select CONCAT(cust.FirstName,' ' ,cust.LastName)
				   from
					customers  cust
				   where
					cust.CustomerId = gen.CustomerId
				   limit 1
				) as CustomerName,
				(
				   select cust.BirthDate
				   from
					customers  cust
				   where
					cust.CustomerId = gen.CustomerId
				   limit 1
				) as BirthDate,				
				(
				   select cust.Email
				   from
					customers  cust
				   where
					cust.CustomerId = gen.CustomerId
				   limit 1
				) as Email,
				map.ClientId,
				(
				   select clnt.CompanyName
				   from clients clnt
				   where
					clnt.ClientId = map.ClientId
				   limit 1
				) as ClientName,
				gen.CouponId,
				gen.DateRedeemed,
				map.CouponName,
				map.CouponType,
				IFNULL(map.PointsValue,0) PointsValue
			FROM 
				coupon map,
				generated_coupons gen
			WHERE   1=1
				AND gen.PointsId   = map.PointsId
				AND gen.Status     != 'PENDING'
				AND gen.CouponId   = map.CouponId
				$xtra
				$filter
				$filterX1
				$filterX2
				$filterX3
				$dtfilter1
				$dtfilter2
				$dtfilter3
			ORDER BY gen.DateRedeemed DESC
		";		
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProvider    = new CSqlDataProvider($rawData, array(
					    'keyField'       => 'GeneratedCouponId',
					    'totalItemCount' => $rawCount,
					    )
			);
		
		}
    		
		$mapping =  $this->getMoreLists();
	

		//get csv
		$csv = $this->formatCsvRedeemCoupons($rawSql,$criteria,null);
		
		
		$this->render('redeemcoupons',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
			'model'        => ReportsList::model(),
			'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),
			
		));
	}

	protected function formatCsvRedeemCoupons($rawSql, $criteria, $sort)
	{
		$fn   = sprintf("%s-RedeemCoupons-%s-%s-%s.csv",Yii::app()->params['reportPfx'],@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//ensure
		if (!@file_exists(Yii::app()->params['reportCsv'])) {
		    @mkdir(Yii::app()->params['reportCsv'], 0777, true);
		}
		
		//fmt it her		
		$rawData       = Yii::app()->db->createCommand($rawSql); 
		$rawCount      = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dbprovider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'GeneratedCouponId',
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
		);

		//set
		$dbprovider->setPagination(false);
		$total = 0;
		
		//hdr
		$hdr_ttl = array("SEQ. NO.",
				 "CUSTOMER ID",
				 "CUSTOMER NAME",
				 "CUSTOMER EMAIL",
				 "CUSTOMER BDAY",
				 "COMPANY NAME",
				 "POINTS NAME",
				 "COUPON NAME",
				 "COUPON TYPE",
				 "COUPON CODE",
				 "POINTS EQUIVALENT",
				 "DATE CREATED",
			 );

		$utils = new Utils;
		$hdr   = $utils->fmt_csv($hdr_ttl);
		
		$utils->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		$total = 0;
		//get csv
		foreach($dbprovider->getData() as $row) 
		{
			
			$total++;
			//customer
			$custmail = $row["Email"];
			$custuid  = $row["CustomerId"];
			$custbday = $row["BirthDate"];
			$custname = $row["CustomerName"];
			
			//comp
			$compname  = $row["ClientName"];

			//campaign
			$ptsname   = $row["PointsSystemName"];
			
			//brand
			$couponName  = $row["CouponName"];
			$couponType  = $row["CouponType"];
			$couponCode  = $row["Code"];
			
			
			//pts
			$tpts      = $row["PointsValue"];

			//hdr
			$ts        = $row["DateRedeemed"];
						           

			//fmt
			$udata   = array();
			$udata[] = trim($total     );
			$udata[] = trim($custuid   );
			$udata[] = trim($custname  );
			$udata[] = trim($custmail  );
			$udata[] = trim($custbday  );
			$udata[] = trim($compname  );
			$udata[] = trim($ptsname   );
			$udata[] = trim($couponName);
			$udata[] = trim($couponType);
			$udata[] = trim($couponCode);
			$udata[] = trim($tpts      );   
			$udata[] = trim($ts        );  
			
			//fmt
			$str   = $utils->fmt_csv($udata);

			$utils->io_save($csv, str_replace("\n",'', $str)."\n",'a');
		}
		
		//give it back
		return array(
			'total' => $total,
			'fn'    => $fn
		);
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
			$rawSql = "
				select DISTINCT a.CustomerId, 
				       a.SubscriptionId, 
					   a.PointsId,
					   /**
					   a.ClientId, 
				       a.BrandId, 
				       a.CampaignId, 
				       a.status SubsriptionStatus,
				       b.Balance, 
				       b.Used, 
				       b.Total,
					   f.CompanyName,
					   a.PointsId,
					   **/
				       e.Email,
				       e.FirstName,
				       e.LastName,
					   e.BirthDate,
					(
						select pts.Name
						from
						 points pts
						where
						pts.PointsId = a.PointsId
						limit 1	
					) as PointsSystemName,
					(
						select pts.DateCreated
						from
						 points_log pts
						where
							pts.PointsId     = a.PointsId
						and 
							pts.PointLogId   = ( select max(bb.PointLogId) FROM points_log bb WHERE bb.PointsId = a.PointsId )
							limit 1
					) as PointsSystemDate,
					(
					   select CONCAT(cust.FirstName,' ' ,cust.LastName)
					   from
						customers  cust
					   where
						cust.CustomerId = e.CustomerId
					   limit 1
					) as CustomerName,
					(
					   select SUM(IFNULL(bb.Balance,0)) 
					    from
						customer_points bb
					    where
						bb.SubscriptionId = a.SubscriptionId
						and
						bb.PointsId       = a.PointsId
					    limit 1
					) as Balance,
					(
					   select SUM(IFNULL(bb.Used,0)) 
					    from
						customer_points bb
					    where
						bb.SubscriptionId = a.SubscriptionId
						and
						bb.PointsId       = a.PointsId
					    limit 1

					) as Used,
					(
					   select SUM(IFNULL(bb.Total,0)) 
					    from
						customer_points bb
					    where
						bb.SubscriptionId = a.SubscriptionId
						and
						bb.PointsId       = a.PointsId
					    limit 1
					) as Total,
					a.DateCreated
				from  customer_subscriptions a, 
				      customers e, clients f
				WHERE 1=1
					and   a.CustomerId     = e.CustomerId
					and   a.ClientId       = f.ClientId
					and   a.Status         = 'ACTIVE'
					$xtra
					$vxtra
					$filter
				GROUP BY a.CustomerId
				";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
						    'keyField'       => 'SubscriptionId',
						    'totalItemCount' => $rawCount,
						    )
				);
		
		}
		if(0)
		{
			echo "<hr><pre>$rawSql</pre><hr>";
			exit;
			
		}
		$mapping =  $this->getMoreLists();

		//get csv
		$csv = $this->formatCsvCustomerActivity($rawSql,null,null);
		
		$this->render('customeractivity',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
			'model'        => ReportsList::model(),
			'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),
		));
	}
	
	
	protected function formatCsvCustomerActivity($rawSql, $criteria, $sort)
	{
		$fn   = sprintf("%s-CustomerActivity-%s-%s-%s.csv",Yii::app()->params['reportPfx'],@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//ensure
		if (!@file_exists(Yii::app()->params['reportCsv'])) {
		    @mkdir(Yii::app()->params['reportCsv'], 0777, true);
		}
		
		//fmt it her		
		$rawData       = Yii::app()->db->createCommand($rawSql); 
		$rawCount      = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dbprovider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'SubscriptionId',
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
		);

		//set
		$dbprovider->setPagination(false);
		$total = 0;
		
		//hdr
		$hdr_ttl = array("SEQ. NO.",
				 "CUSTOMER ID",
				 "CUSTOMER NAME",
				 "CUSTOMER EMAIL",
				 "CUSTOMER BDAY",
				 "POINTS NAME",
				 "DATE CREATED",
			 );

		$utils = new Utils;
		$hdr   = $utils->fmt_csv($hdr_ttl);
		
		$utils->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		$total = 0;
		//get csv
		foreach($dbprovider->getData() as $row) 
		{
			
			$total++;
			//customer
			$custmail = $row["Email"];
			$custuid  = $row["CustomerId"];
			$custbday = $row["BirthDate"];
			$custname = $row["CustomerName"];
			
			//comp

			//campaign
			$ptsname   = $row["PointsSystemName"];
			
			
			//hdr
			$ts        = $row["PointsSystemDate"];
						           

			//fmt
			$udata   = array();
			$udata[] = trim($total     );
			$udata[] = trim($custuid   );
			$udata[] = trim($custname  );
			$udata[] = trim($custmail  );
			$udata[] = trim($custbday  );
			$udata[] = trim($ptsname   );
			$udata[] = trim($ts        );  
			
			//fmt
			$str   = $utils->fmt_csv($udata);

			$utils->io_save($csv, str_replace("\n",'', $str)."\n",'a');
		}
		
		//give it back
		return array(
			'total' => $total,
			'fn'    => $fn
		);
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
				       a.DateCreated,
				       a.PointsId, 
				       a.Value Points,
				       f.CompanyName, g.BrandName, h.CampaignName, i.ChannelName
				from  points_log a, 
				      points b,
				      customers e,clients f,brands g,campaigns h, channels i
				where   a.PointsId = b.PointsId
					and   a.CustomerId     = e.CustomerId
					and   a.ClientId       = f.ClientId
					and   a.BrandId        = g.BrandId
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
					       a.DateCreated,
					       ifnull(a.PointsId,0), a.Value Points,
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
				$xtra   = " AND rdm.ClientId = '$clid'  ";
			}
			
			$filter     = '';
			$filterSrch = 0;
			//points-name
			$byPointsName   = trim(Yii::app()->request->getParam('byPointsName'));
			$filterX1       = '';
			if(strlen($byPointsName))
			{
				$filterSrch++;
				$t = addslashes($byPointsName);
				$filterX1  = " AND EXISTS (
										SELECT 1
										From points pts
										Where 
											pts.PointsId = rdm.PointsId
											AND pts.Name LIKE '%$t%'
										LIMIT 1
								) ";
			}
			//rewards-name
			$byRewardDetailsName   = trim(Yii::app()->request->getParam('byRewardDetailsName'));
			$filterX2       = '';
			if(strlen($byRewardDetailsName))
			{
				$filterSrch++;
				$t = addslashes($byRewardDetailsName);
				$filterX2  = " AND ( dtls.Name LIKE '%$t%' ) ";
			}
			//customer-name,
			$byCustomer   = trim(Yii::app()->request->getParam('byCustomer'));
			$filterX3       = '';
			if(strlen($byCustomer))
			{
				$filterSrch++;
				$t = addslashes($byCustomer);
				$filterX3  = " AND EXISTS (
										SELECT 1
									   from
										customers  custm
									   where
										custm.CustomerId = cust.CustomerId
										AND (
											custm.FirstName LIKE '%$t%'
											or 
											custm.LastName  LIKE '%$t%'
										)
										LIMIT 1
						   	) ";
			}
			
			//date: 
			$byTranDateFr = trim(Yii::app()->request->getParam('byTranDateFr'));
			$dtfilter1     = '';
			if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateFr))
			{
				$filterSrch++;
				$t = addslashes($byTranDateFr);
				$dtfilter1 = " AND ( rdm.DateRedeemed >= '$t 00:00:00' ) ";
			}
			//date: 
			$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
			$dtfilter2     = '';
			if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
			{
				$filterSrch++;
				$t = addslashes($byTranDateTo);
				$dtfilter2 = " AND ( rdm.DateRedeemed <= '$t 23:59:59' ) ";
			}
					
			//by client
			$dtfilter3 = '';
			if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN' and isset($_REQUEST['Clients'])) 
			{
				$byClient = $_REQUEST['Clients']['ClientId'];
				if($byClient>0)
				{
					$t = addslashes($byClient);
					$dtfilter3 = " AND rdm.ClientId = '$t' ";
				}			
			}	
			
			if(1){
				$rawSql = "
					SELECT  DISTINCT
						rdm.PointsId,
						cust.CustomerId ,
						rdm.ClientId   ,
						(
							select clnt.CompanyName
							from
								clients clnt
							where
								clnt.ClientId = cust.ClientId
							LIMIT 1
						) as CompanyName ,
						rlist.RewardId  ,
						rlist.Title     ,
						rlist.Description     ,
						rlist.Image,
						(
							SELECT pts.Name 
							From points pts
							Where pts.PointsId = rdm.PointsId
							LIMIT 1
						) as PointsSystemName,
						rdm.DateRedeemed,
						(
							select dtls.Value
							from
							reward_details dtls
							where
							  dtls.RewardId = rlist.RewardId 
							  and
							  dtls.ClientId = cust.ClientId
							  and
							  dtls.PointsId = rdm.PointsId
							limit 1
						) as Pts,
						(
						   select CONCAT(custm.FirstName,' ' ,custm.LastName)
						   from
							customers  custm
						   where
							custm.CustomerId = cust.CustomerId
						   limit 1
						) as CustomerName,
						(
						   select custm.BirthDate
						   from
							customers  custm
						   where
							custm.CustomerId = cust.CustomerId
						   limit 1
						) as BirthDate,						
						(
						   select custm.Email
						   from
							customers  custm
						   where
							custm.CustomerId = cust.CustomerId
						   limit 1
						) as Email,
						rdm.RedeemedId,
						dtls.Name as DetailsName,
						dtls.RewardConfigId as DetailsId,
						dtls.CreatedBy
					FROM
						customers cust,
						rewards_list rlist,
						reward_details dtls,
						redeemed_reward rdm
					WHERE
					1=1
						AND rdm.ClientId   = rlist.ClientId
						AND rdm.ClientId   = dtls.ClientId
						AND rdm.UserId     = cust.CustomerId
						AND rlist.Status  IN ('ACTIVE')
						AND cust.Status   IN ('ACTIVE')
						AND rlist.RewardId  = rdm.RewardId
						AND rdm.PointsId    = dtls.PointsId
						AND rlist.RewardId  = dtls.RewardId
						AND rdm.RewardId    = dtls.RewardId
						$xtra  $filter
						$filterX1
						$filterX2
						$filterX3
						$dtfilter1
						$dtfilter2
						$dtfilter3
					ORDER BY rdm.DateRedeemed DESC	
					";
			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
						    'keyField'       => 'RedeemedId',
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


			//get csv
			$csv = $this->formatCsvRedeemRewards($rawSql,null,null);
			
		
			$this->render('redeemrewards',array(
				'dataProvider' => $dataProvider,
				'mapping'      => $mapping,
				'model'        => ReportsList::model(),
				'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),
			));
	}	
	
	protected function formatCsvRedeemRewards($rawSql, $criteria, $sort)
	{
		$fn   = sprintf("%s-RedeemRewards-%s-%s-%s.csv",Yii::app()->params['reportPfx'],@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//ensure
		if (!@file_exists(Yii::app()->params['reportCsv'])) {
		    @mkdir(Yii::app()->params['reportCsv'], 0777, true);
		}
		
		//fmt it her		
		$rawData       = Yii::app()->db->createCommand($rawSql); 
		$rawCount      = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dbprovider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'RedeemedId',
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
		);

		//set
		$dbprovider->setPagination(false);
		$total = 0;
		
		//hdr
		$hdr_ttl = array("SEQ. NO.",
				 "CUSTOMER ID",
				 "CUSTOMER NAME",
				 "CUSTOMER EMAIL",
				 "CUSTOMER BDAY",
				 "COMPANY NAME",
				 "POINTS NAME",
				 "REWARDS DETAILS NAME",
				 "POINTS EQUIVALENT",
				 "DATE CREATED",
			 );

		$utils = new Utils;
		$hdr   = $utils->fmt_csv($hdr_ttl);
		
		$utils->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		$total = 0;
		//get csv
		foreach($dbprovider->getData() as $row) 
		{
			
			$total++;
			//customer
			$custmail = $row["Email"];
			$custuid  = $row["CustomerId"];
			$custbday = $row["BirthDate"];
			$custname = $row["CustomerName"];
			
			//comp
			$compname  = $row["CompanyName"];

			//campaign
			$ptsname   = $row["PointsSystemName"];
			
			//brand
			$dtlsName  = $row["DetailsName"];

			

			//by
			$by        = $row["CreatedBy"];

			//pts
			$tpts      = $row["Pts"];

			//hdr
			$ts       = $row["DateRedeemed"];
						           

			//fmt
			$udata   = array();
			$udata[] = trim($total     );
			$udata[] = trim($custuid   );
			$udata[] = trim($custname  );
			$udata[] = trim($custmail  );
			$udata[] = trim($custbday  );
			$udata[] = trim($compname  );
			$udata[] = trim($ptsname   );
			$udata[] = trim($dtlsName  );
			$udata[] = trim($tpts      );   
			$udata[] = trim($ts        );  
			//fmt
			$str   = $utils->fmt_csv($udata);

			$utils->io_save($csv, str_replace("\n",'', $str)."\n",'a');
		}
		
		//give it back
		return array(
			'total' => $total,
			'fn'    => $fn
		);
	}




	
	public function actionCampaignPart()
	{
			
		//criteria
		$criteria   = new CDbCriteria;
		$filterSrch = 0;
		
		//channel-name
		$byChannel   = trim(Yii::app()->request->getParam('byChannel'));
		$ofilter     = '';
		if(strlen($byChannel))
		{
			$filterSrch++;
			$t       = addslashes($byChannel);
			$ofilter = " AND EXISTS (
									SELECT 1
									From channels chan
									Where 
										chan.ChannelId = ptslog.ChannelId
										AND 
										chan.ChannelName LIKE '%$t%'
									LIMIT 1
							) ";

		}
		//campaign
		$byCampaign   = trim(Yii::app()->request->getParam('byCampaign'));
		$pfilter     = '';
		if(strlen($byCampaign))
		{
			$filterSrch++;
			$t       = addslashes($byCampaign);
			$pfilter = " AND EXISTS (
									SELECT 1
									From campaigns camp
									Where 
										camp.CampaignId = ptslog.CampaignId
										AND 
										camp.CampaignName LIKE '%$t%'
									LIMIT 1
							) ";
		}
		//brand
		$byBrand   = trim(Yii::app()->request->getParam('byBrand'));
		$qfilter     = '';
		if(strlen($byBrand))
		{
			$filterSrch++;
			$t = addslashes($byBrand);
			$qfilter = " AND EXISTS (
									SELECT 1
									From brands brnd
									Where 
										brnd.BrandId    = ptslog.BrandId
										AND 
										brnd.BrandName LIKE '%$t%'
									LIMIT 1
							) ";
		}
		//customer
		$byClientName = trim(Yii::app()->request->getParam('byClientName'));
		$rfilter      = '';
		if(strlen($byClientName))
		{
			$filterSrch++;
			$t = addslashes($byClientName);
			$rfilter = " AND (
						 clnt.CompanyName LIKE '%".addslashes($byClientName)."%'
				     ) ";
		}
		//date: 
		$byTranDateFr = trim(Yii::app()->request->getParam('byTranDateFr'));
		$dtfilter1     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateFr))
		{
			$filterSrch++;
			$t = addslashes($byTranDateFr);
			$dtfilter1 = " AND ( ptslog.DateCreated >= '$t 00:00:00' ) ";
		}
		//date: 
		$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
		$dtfilter2     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
		{
			$filterSrch++;
			$t = addslashes($byTranDateTo);
			$dtfilter2 = " AND ( ptslog.DateCreated <= '$t 23:59:59' ) ";
		}
				
		
		//no-filter
		$sfilter     = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$qid     =  addslashes(Yii::app()->user->ClientId);
			$sfilter = " AND ptslog.ClientId = '$qid' ";
		}


		//email + cust-name + etc
		if('sortby' == 'sortby')
		{
				// set sort options
				$sort = new CSort;
				$sort->attributes = array(
								'EmailAdd'       => array(
										'asc'    =>'cust.Email',
										'desc'   =>'cust.Email DESC',
										'label'  =>'Email',
								),
								'CustomerNm'  => array(
										'asc'    =>'cust.LastName',
										'desc'   =>'cust.LastName DESC',
										'label'  =>'Customer Name',
								),
								'*',
				);
				//$sort->multiSort  = true;
		}

		//show columns
		$show_clnt = array(
				'ClientId',
				'CompanyName',
				'PointsName',
				'participants',
				'DateCreated',
				'PointsId',
				);

		$show_brnd = array(              
				'BrandId'     ,
				'BrandName'   ,
				'PointsName'  ,
				'CompanyName' ,
				'participants',
				'DateCreated' ,
				'PointsId'    ,
				'ClientId',
				);

		$show_camp = array(              
				'CampaignId'     ,
				'CampaignName'   ,
				'BrandName'      ,
				'PointsName'   ,
				'CompanyName'  ,
				'participants' ,
				'DateCreated'  ,
				'PointsId'     ,
				'ClientId'     ,
				);

		$show_chan = array(                         
				'ChannelId'    ,
				'ChannelName'  ,
				'BrandName'    ,
				'CampaignName' ,
				'PointsName'   ,
				'CompanyName'  ,
				'participants' ,
				'DateCreated'  ,
				'PointsId'     ,
				'ClientId'     ,
				);   
		//show columns
		$downloadCSV = array();
	
		//BY CLIENT
		if(1){
		$rawSql   = "
				SELECT 
					  ptslog.ClientId,
					  clnt.CompanyName,
					  pts.Name as PointsName,
					  COUNT(distinct(cust.CustomerId)) as participants,
					  MAX(ptslog.DateCreated) DateCreated,
					  pts.PointsId
				FROM 
					  points_log ptslog,
					  customers cust,
					  clients clnt,
					  points pts,
					  action_type act
				WHERE 1=1
					  AND   ptslog.CustomerId     = cust.CustomerId
					  AND   ptslog.ClientId       = clnt.ClientId
					  AND   ptslog.ActiontypeId   = act.ActiontypeId
					  AND   ptslog.PointsId       = pts.PointsId
					  AND   pts.PointsId          = act.PointsId
						$ofilter 
						$pfilter 
						$qfilter 
						$rfilter 
						$sfilter 
						$dtfilter1
						$dtfilter2
					GROUP BY
							ptslog.ClientId,
							clnt.CompanyName,
							PointsName
					ORDER BY DateCreated  DESC
			";
		}
	
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProviderClient    = new CSqlDataProvider($rawData, array(
						'keyField'       => 'ClientId',
						'totalItemCount' => $rawCount,
						'sort'           => $sort,
						)
				);

		//get csv
		$csv                    = $this->formatCsvCampaignpart($show_clnt,'ClientId',$rawSql, null, null);
		$downloadCSV['CLIENT']  = (@intval($csv['total'])>0)?($csv['fn']):('');

				
				
		//BY BRAND
		if(1){
		$rawSql   = "
				SELECT 
					  ptslog.BrandId        ,
					  brnd.BrandName        ,
					  pts.Name as PointsName,
					  clnt.CompanyName,
					  COUNT(distinct(cust.CustomerId)) as participants,
					  MAX(ptslog.DateCreated) DateCreated,
					  pts.PointsId,
					  ptslog.ClientId
				FROM 
					  points_log ptslog,
					  customers cust,
					  clients clnt,
					  points pts,
					  action_type act,
					  brands brnd
				WHERE 1=1
					  AND   ptslog.CustomerId     = cust.CustomerId
					  AND   ptslog.ClientId       = clnt.ClientId
					  AND   ptslog.ActiontypeId   = act.ActiontypeId
					  AND   ptslog.PointsId       = pts.PointsId
					  AND   pts.PointsId          = act.PointsId
					  AND   ptslog.BrandId        = brnd.BrandId
						$ofilter 
						$pfilter 
						$qfilter 
						$rfilter 
						$sfilter 
						$dtfilter1
						$dtfilter2
				GROUP BY 
						ptslog.BrandId        ,
						brnd.BrandName        ,
						PointsName
				ORDER BY DateCreated DESC
			";
		}
	
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProviderBrand    = new CSqlDataProvider($rawData, array(
						'keyField'       => 'BrandId',
						'totalItemCount' => $rawCount,
						'sort'           => $sort,
						)
				);

		//get csv
		$csv                    = $this->formatCsvCampaignpart($show_brnd,'BrandId',$rawSql, null, null);
		$downloadCSV['BRAND']   = (@intval($csv['total'])>0)?($csv['fn']):('');		

				
		//BY CAMPAIGN
		if(1){
		$rawSql   = "
				SELECT 
					ptslog.CampaignId    ,
					camp.CampaignName    ,
					(
						select brnd.BrandName
						from
						brands brnd
						where
						  brnd.BrandId = ptslog.BrandId
						limit 1 
					) as BrandName ,
					pts.Name as PointsName,
					clnt.CompanyName,
					COUNT(distinct(cust.CustomerId)) as participants,
					MAX(ptslog.DateCreated) DateCreated,
				    pts.PointsId,
				    ptslog.ClientId
				FROM 
					  points_log ptslog,
					  customers cust,
					  clients clnt,
					  points pts,
					  action_type act,
					  campaigns camp
				WHERE 1=1
					  AND   ptslog.CustomerId     = cust.CustomerId
					  AND   ptslog.ClientId       = clnt.ClientId
					  AND   ptslog.ActiontypeId   = act.ActiontypeId
					  AND   ptslog.PointsId       = pts.PointsId
					  AND   pts.PointsId          = act.PointsId
					  AND   ptslog.CampaignId     = camp.CampaignId
						$ofilter 
						$pfilter 
						$qfilter 
						$rfilter 
						$sfilter 
						$dtfilter1
						$dtfilter2
				GROUP BY 
						ptslog.CampaignId     ,
						camp.CampaignName     ,
						PointsName
				ORDER BY DateCreated DESC
			";
		}
	
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProviderCampaign   = new CSqlDataProvider($rawData, array(
						'keyField'       => 'CampaignId',
						'totalItemCount' => $rawCount,
						'sort'           => $sort,
						)
				);

		//get csv
		$csv                    = $this->formatCsvCampaignpart($show_camp,'CampaignId',$rawSql, null, null);
		$downloadCSV['CAMPAIGN']= (@intval($csv['total'])>0)?($csv['fn']):('');


		//BY CHANNEL 
		if(1){
		$rawSql   = "
				SELECT 
					ptslog.ChannelId     ,
					chan.ChannelName     ,
					(
						select brnd.BrandName
						from
						brands brnd
						where
						  brnd.BrandId = ptslog.BrandId
						limit 1 
					) as BrandName ,
					(
						select camp.CampaignName
						from
						campaigns camp
						where
						  camp.CampaignId = ptslog.CampaignId
						limit 1 
					) as CampaignName ,
					pts.Name as PointsName,
					clnt.CompanyName,
					COUNT(distinct(cust.CustomerId)) as participants,
					MAX(ptslog.DateCreated) DateCreated,
				    pts.PointsId,
				    ptslog.ClientId
				FROM 
					  points_log ptslog,
					  customers cust,
					  clients clnt,
					  points pts,
					  action_type act,
					  channels chan
				WHERE 1=1
					  AND   ptslog.CustomerId     = cust.CustomerId
					  AND   ptslog.ClientId       = clnt.ClientId
					  AND   ptslog.ActiontypeId   = act.ActiontypeId
					  AND   ptslog.PointsId       = pts.PointsId
					  AND   pts.PointsId          = act.PointsId
					  AND   ptslog.ChannelId      = chan.ChannelId
						$ofilter 
						$pfilter 
						$qfilter 
						$rfilter 
						$sfilter 
						$dtfilter1
						$dtfilter2
			GROUP BY 
					ptslog.ChannelId ,
					chan.ChannelName ,
					PointsName
			ORDER BY DateCreated DESC
			";
		}
	
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProviderChannel   = new CSqlDataProvider($rawData, array(
						'keyField'       => 'ChannelId',
						'totalItemCount' => $rawCount,
						'sort'           => $sort,
						)
				);

		//get csv
		$csv                    = $this->formatCsvCampaignpart($show_chan,'ChannelId',$rawSql, null, null);
		$downloadCSV['CHANNEL'] = (@intval($csv['total'])>0)?($csv['fn']):('');

		$this->render('campaignpart',array(
			'dataProviderClient'    => $dataProviderClient,
			'dataProviderBrand'     => $dataProviderBrand,
			'dataProviderCampaign'  => $dataProviderCampaign,
			'dataProviderChannel'   => $dataProviderChannel,
			'model'                 => ReportsList::model(),
			'downloadCSV'           => $downloadCSV,
		));
		
	
	}	

	protected function formatCsvCampaignpart($show=array(),$keyField,$rawSql, $criteria, $sort)
	{
		$fn   = sprintf("%s-CAMPAIGN-PART-$keyField-%s-%s-%s.csv",Yii::app()->params['reportPfx'],@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//ensure
		if (!@file_exists(Yii::app()->params['reportCsv'])) {
		    @mkdir(Yii::app()->params['reportCsv'], 0777, true);
		}
		
		//fmt it her		
		$rawData       = Yii::app()->db->createCommand($rawSql); 
		$rawCount      = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dbprovider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => $keyField,
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
		);

		//set
		$dbprovider->setPagination(false);
		$total = 0;
		
		//hdr
		$hdr_ttl = $show;
		$utils = new Utils;
		$hdr   = $utils->fmt_csv($hdr_ttl);
		
		$utils->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		$total  = 0;
		$sumall = 0;
		//get csv
		foreach($dbprovider->getData() as $row) 
		{
			
			$total++;
			//customer

			//fmt
			$udata   = array();
			foreach($show as $rec)
			{
				$udata[] = trim($row["$rec"]);
			}
			//fmt
			$str   = $utils->fmt_csv($udata);

			$utils->io_save($csv, str_replace("\n",'', $str)."\n",'a');
		}
		
		//give it back
		return array(
			'total' => $total,
			'fn'    => $fn
		);
	}

	
	//CLIENT
	public function actionCustparticipation1()
	{
		$PointsId = @intval( trim(Yii::app()->request->getParam('PointsId')) );
		$ClientId = @intval( trim(Yii::app()->request->getParam('ClientId')) );
		
		//criteria
		$criteria   = new CDbCriteria;
		$filterSrch = 0;
		
		//no-filter
		$sfilter     = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$qid     =  addslashes(Yii::app()->user->ClientId);
			$sfilter = " AND ptslog.ClientId = '$qid' ";
		}
		else
		{
			$qid     =  addslashes($ClientId);
			$sfilter = " AND ptslog.ClientId = '$qid' ";
		}
		
	    $tfilter     = '';
		if($PointsId > 0)  
		{
			$t       =  addslashes($PointsId);
			$tfilter = " AND ptslog.PointsId = '$t' ";
		}
		
		//email + cust-name + etc
		if('sortby' == 'sortby')
		{
				// set sort options
				$sort = new CSort;
				$sort->attributes = array(
								'EmailAdd'       => array(
										'asc'    =>'cust.Email',
										'desc'   =>'cust.Email DESC',
										'label'  =>'Email',
								),
								'CustomerNm'  => array(
										'asc'    =>'cust.LastName',
										'desc'   =>'cust.LastName DESC',
										'label'  =>'Customer Name',
								),
								'*',
				);
				//$sort->multiSort  = true;
		}
	

		//BY CLIENT
		if(1){
		$rawSql   = "
				SELECT 
					cust.CustomerId     ,
					CONCAT(cust.LastName,' ' ,cust.FirstName) as CustomerName,
					cust.Email          ,
					cust.BirthDate      ,
					clnt.CompanyName    ,
					(
						select brnd.BrandName
						from
						brands brnd
						where
						  brnd.BrandId = ptslog.BrandId
						limit 1 
					) as BrandName ,
					(
						select camp.CampaignName
						from
						campaigns camp
						where
						  camp.CampaignId = ptslog.CampaignId
						limit 1 
					) as CampaignName ,
					(
						select chan.ChannelName
						from
						channels chan
						where
						  chan.ChannelId = ptslog.ChannelId
						limit 1 
					) as ChannelName ,					
					pts.Name as PointsName,
					MAX(ptslog.DateCreated) as DateCreated
				FROM 
					  points_log ptslog,
					  customers cust,
					  clients clnt,
					  points pts,
					  action_type act
				WHERE 1=1
					  AND   ptslog.CustomerId     = cust.CustomerId
					  AND   ptslog.ClientId       = clnt.ClientId
					  AND   ptslog.ActiontypeId   = act.ActiontypeId
					  AND   ptslog.PointsId       = pts.PointsId
					  AND   pts.PointsId          = act.PointsId
						$sfilter 
						$tfilter 
				GROUP BY 
					  cust.CustomerId
					ORDER BY DateCreated  DESC
			";
		}
	
	
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProviderChannel   = new CSqlDataProvider($rawData, array(
						'keyField'       => 'CustomerId',
						'totalItemCount' => $rawCount,
						'sort'           => $sort,
						)
				);

		//get csv
		$csv = $this->formatCsvCustparticipation($rawSql,null,null);
				
				
		$this->render('custparticipation',array(
			'dataProviderChannel'   => $dataProviderChannel,
			'model'                 => ReportsList::model(),
			'whatMode'              => '(By Client)',
			'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),
		));
		
	
	}	
	
	//BRAND
	public function actionCustparticipation2()
	{

		$PointsId = @intval( trim(Yii::app()->request->getParam('PointsId')) );
		$ClientId = @intval( trim(Yii::app()->request->getParam('ClientId')) );
		$BrandId  = @intval( trim(Yii::app()->request->getParam('BrandId')) );	
			
		//criteria
		$criteria   = new CDbCriteria;
		$filterSrch = 0;
		
		//no-filter
		$sfilter     = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$qid     =  addslashes(Yii::app()->user->ClientId);
			$sfilter = " AND ptslog.ClientId = '$qid' ";
		}
		else
		{
			$qid     =  addslashes($ClientId);
			$sfilter = " AND ptslog.ClientId = '$qid' ";
		}
	    $tfilter     = '';
		if($PointsId > 0)  
		{
			$t       =  addslashes($PointsId);
			$tfilter = " AND ptslog.PointsId = '$t' ";
		}
	    $ufilter     = '';
		if($BrandId > 0)  
		{
			$t       =  addslashes($BrandId);
			$ufilter = " AND ptslog.BrandId = '$t' ";
		}
				
		//email + cust-name + etc
		if('sortby' == 'sortby')
		{
				// set sort options
				$sort = new CSort;
				$sort->attributes = array(
								'EmailAdd'       => array(
										'asc'    =>'cust.Email',
										'desc'   =>'cust.Email DESC',
										'label'  =>'Email',
								),
								'CustomerNm'  => array(
										'asc'    =>'cust.LastName',
										'desc'   =>'cust.LastName DESC',
										'label'  =>'Customer Name',
								),
								'*',
				);
				//$sort->multiSort  = true;
		}
	
		//BY BRAND
		if(1){
		$rawSql   = "
				SELECT 
					cust.CustomerId     ,
					CONCAT(cust.LastName,' ' ,cust.FirstName) as CustomerName,
					cust.Email          ,
					cust.BirthDate      ,
					ptslog.ChannelId    ,
					clnt.CompanyName    ,
					(
						select brnd.BrandName
						from
						brands brnd
						where
						  brnd.BrandId = ptslog.BrandId
						limit 1 
					) as BrandName ,
					(
						select camp.CampaignName
						from
						campaigns camp
						where
						  camp.CampaignId = ptslog.CampaignId
						limit 1 
					) as CampaignName ,
					(
						select chan.ChannelName
						from
						channels chan
						where
						  chan.ChannelId = ptslog.ChannelId
						limit 1 
					) as ChannelName ,					
					pts.Name as PointsName,
					MAX(ptslog.DateCreated) as DateCreated    
				FROM 
					  points_log ptslog,
					  customers cust,
					  clients clnt,
					  points pts,
					  action_type act,
					  brands brnd
				WHERE 1=1
					  AND   ptslog.CustomerId     = cust.CustomerId
					  AND   ptslog.ClientId       = clnt.ClientId
					  AND   ptslog.ActiontypeId   = act.ActiontypeId
					  AND   ptslog.PointsId       = pts.PointsId
					  AND   pts.PointsId          = act.PointsId
					  AND   ptslog.BrandId        = brnd.BrandId
						$sfilter 
						$tfilter 
						$ufilter 
				GROUP BY 
						cust.CustomerId
				ORDER BY DateCreated DESC
			";
		}
	
	
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProviderChannel   = new CSqlDataProvider($rawData, array(
						'keyField'       => 'CustomerId',
						'totalItemCount' => $rawCount,
						'sort'           => $sort,
						)
				);

		//get csv
		$csv = $this->formatCsvCustparticipation($rawSql,null,null);
				
				
		$this->render('custparticipation',array(
			'dataProviderChannel'   => $dataProviderChannel,
			'model'                 => ReportsList::model(),
			'whatMode'              => '(By Brand)',
			'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),
		));
		
	
	}	
	
	//CAMPAIGN
	public function actionCustparticipation3()
	{
		$PointsId    = @intval( trim(Yii::app()->request->getParam('PointsId')) );
		$ClientId    = @intval( trim(Yii::app()->request->getParam('ClientId')) );
		$CampaignId  = @intval( trim(Yii::app()->request->getParam('CampaignId')) );	
			
		//criteria
		$criteria   = new CDbCriteria;
		$filterSrch = 0;
		
		//no-filter
		$sfilter     = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$qid     =  addslashes(Yii::app()->user->ClientId);
			$sfilter = " AND ptslog.ClientId = '$qid' ";
		}
		else
		{
			$qid     =  addslashes($ClientId);
			$sfilter = " AND ptslog.ClientId = '$qid' ";
		}
	    $tfilter     = '';
		if($PointsId > 0)  
		{
			$t       =  addslashes($PointsId);
			$tfilter = " AND ptslog.PointsId = '$t' ";
		}
	    $ufilter     = '';
		if($CampaignId > 0)  
		{
			$t       =  addslashes($CampaignId);
			$tfilter = " AND ptslog.CampaignId = '$t' ";
		}
		
		//email + cust-name + etc
		if('sortby' == 'sortby')
		{
				// set sort options
				$sort = new CSort;
				$sort->attributes = array(
								'EmailAdd'       => array(
										'asc'    =>'cust.Email',
										'desc'   =>'cust.Email DESC',
										'label'  =>'Email',
								),
								'CustomerNm'  => array(
										'asc'    =>'cust.LastName',
										'desc'   =>'cust.LastName DESC',
										'label'  =>'Customer Name',
								),
								'*',
				);
				//$sort->multiSort  = true;
		}
 	
		//BY CAMPAIGN
		if(1){
		$rawSql   = "
				SELECT 
						cust.CustomerId     ,
						CONCAT(cust.LastName,' ' ,cust.FirstName) as CustomerName,
						cust.Email          ,
						cust.BirthDate      ,
						ptslog.ChannelId    ,
						clnt.CompanyName    ,
						(
							select brnd.BrandName
							from
							brands brnd
							where
							  brnd.BrandId = ptslog.BrandId
							limit 1 
						) as BrandName ,
						(
							select camp.CampaignName
							from
							campaigns camp
							where
							  camp.CampaignId = ptslog.CampaignId
							limit 1 
						) as CampaignName ,
						(
							select chan.ChannelName
							from
							channels chan
							where
							  chan.ChannelId = ptslog.ChannelId
							limit 1 
						) as ChannelName ,
						pts.Name as PointsName,
						MAX(ptslog.DateCreated) as DateCreated   
				FROM 
					  points_log ptslog,
					  customers cust,
					  clients clnt,
					  points pts,
					  action_type act,
					  campaigns camp
				WHERE 1=1
					  AND   ptslog.CustomerId     = cust.CustomerId
					  AND   ptslog.ClientId       = clnt.ClientId
					  AND   ptslog.ActiontypeId   = act.ActiontypeId
					  AND   ptslog.PointsId       = pts.PointsId
					  AND   pts.PointsId          = act.PointsId
					  AND   ptslog.CampaignId     = camp.CampaignId
						$sfilter 
						$tfilter 
						$ufilter 
				GROUP BY 
						cust.CustomerId
				ORDER BY DateCreated DESC
			";
		}
		
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProviderChannel   = new CSqlDataProvider($rawData, array(
						'keyField'       => 'CustomerId',
						'totalItemCount' => $rawCount,
						'sort'           => $sort,
						)
				);

		//get csv
		$csv = $this->formatCsvCustparticipation($rawSql,null,null);
				
		$this->render('custparticipation',array(
			'dataProviderChannel'   => $dataProviderChannel,
			'model'                 => ReportsList::model(),
			'whatMode'              => '(By Campaign)',
			'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),			
		));
		
	
	}	
	
	//CHANNEL
	public function actionCustparticipation4()
	{
		$PointsId    = @intval( trim(Yii::app()->request->getParam('PointsId')) );
		$ClientId    = @intval( trim(Yii::app()->request->getParam('ClientId')) );
		$ChannelId   = @intval( trim(Yii::app()->request->getParam('ChannelId')) );	

			
		//criteria
		$criteria   = new CDbCriteria;
		$filterSrch = 0;
		
		//no-filter
		$sfilter     = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$qid     =  addslashes(Yii::app()->user->ClientId);
			$sfilter = " AND ptslog.ClientId = '$qid' ";
		}
		else
		{
			$qid     =  addslashes($ClientId);
			$sfilter = " AND ptslog.ClientId = '$qid' ";
		}
	    $tfilter     = '';
		if($PointsId > 0)  
		{
			$t       =  addslashes($PointsId);
			$tfilter = " AND ptslog.PointsId = '$t' ";
		}
	    $ufilter     = '';
		if($ChannelId > 0)  
		{
			$t       =  addslashes($ChannelId);
			$ufilter = " AND ptslog.ChannelId = '$t' ";
		}		
		//email + cust-name + etc
		if('sortby' == 'sortby')
		{
				// set sort options
				$sort = new CSort;
				$sort->attributes = array(
								'EmailAdd'       => array(
										'asc'    =>'cust.Email',
										'desc'   =>'cust.Email DESC',
										'label'  =>'Email',
								),
								'CustomerNm'  => array(
										'asc'    =>'cust.LastName',
										'desc'   =>'cust.LastName DESC',
										'label'  =>'Customer Name',
								),
								'*',
				);
				//$sort->multiSort  = true;
		}
	


		//BY CHANNEL 
		if(1){
		$rawSql   = "
				SELECT 
					cust.CustomerId     ,
					CONCAT(cust.LastName,' ' ,cust.FirstName) as CustomerName,
					cust.Email          ,
					cust.BirthDate      ,
					ptslog.ChannelId    ,
					clnt.CompanyName    ,
					(
						select brnd.BrandName
						from
						brands brnd
						where
						  brnd.BrandId = ptslog.BrandId
						limit 1 
					) as BrandName ,
					(
						select camp.CampaignName
						from
						campaigns camp
						where
						  camp.CampaignId = ptslog.CampaignId
						limit 1 
					) as CampaignName ,
					chan.ChannelName    ,
					pts.Name as PointsName,
					MAX(ptslog.DateCreated) as DateCreated
				FROM 
					  points_log ptslog,
					  customers cust,
					  clients clnt,
					  points pts,
					  action_type act,
					  channels chan
				WHERE 1=1
					  AND   ptslog.CustomerId     = cust.CustomerId
					  AND   ptslog.ClientId       = clnt.ClientId
					  AND   ptslog.ActiontypeId   = act.ActiontypeId
					  AND   ptslog.PointsId       = pts.PointsId
					  AND   pts.PointsId          = act.PointsId
					  AND   ptslog.ChannelId      = chan.ChannelId
						$sfilter 
						$tfilter 
						$ufilter 
			GROUP BY 
					cust.CustomerId
			ORDER BY DateCreated DESC
			";
		}
	
	
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProviderChannel   = new CSqlDataProvider($rawData, array(
						'keyField'       => 'CustomerId',
						'totalItemCount' => $rawCount,
						'sort'           => $sort,
						)
				);

		//get csv
		$csv = $this->formatCsvCustparticipation($rawSql,null,null);
				
		$this->render('custparticipation',array(
			'dataProviderChannel'   => $dataProviderChannel,
			'model'                 => ReportsList::model(),
			'whatMode'              => '(By Channel)',
			'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),	
		));
		
	
	}	
	
	
	protected function formatCsvCustparticipation($rawSql, $criteria, $sort)
	{
		$fn   = sprintf("%s-Participation-%s-%s-%s.csv",Yii::app()->params['reportPfx'],@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//ensure
		if (!@file_exists(Yii::app()->params['reportCsv'])) {
		    @mkdir(Yii::app()->params['reportCsv'], 0777, true);
		}
		
		//fmt it her		
		$rawData       = Yii::app()->db->createCommand($rawSql); 
		$rawCount      = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dbprovider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'CustomerId',
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
		);

		//set
		$dbprovider->setPagination(false);
		$total = 0;
		
		//hdr
		$hdr_ttl = array("SEQ. NO.",
				 "CUSTOMER ID",
				 "CUSTOMER NAME",
				 "CUSTOMER EMAIL",
				 "CUSTOMER BDAY",
				 "COMPANY NAME",
				 "BRAND NAME",
				 "CAMPAIGN NAME",
				 "CHANNEL NAME",
				 "POINTS NAME",
				 "DATE CREATED",
			 );

		$utils = new Utils;
		$hdr   = $utils->fmt_csv($hdr_ttl);
		
		$utils->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		$total = 0;
		//get csv
		foreach($dbprovider->getData() as $row) 
		{
			
			$total++;
			//customer
			$custmail = $row["Email"];
			$custuid  = $row["CustomerId"];
			$custbday = $row["BirthDate"];
			$custname = $row["CustomerName"];
			
			//comp
			$compname  = $row["CompanyName"];

			
			//brand
			$brandname    = $row["BrandName"];
			$campaignname = $row["CampaignName"];
			$channelname  = $row["ChannelName"];
			
			//campaign
			$ptsname   = $row["PointsName"];
			

			//hdr
			$ts       = $row["DateCreated"];
						           

			//fmt
			$udata   = array();
			$udata[] = trim($total     );
			$udata[] = trim($custuid   );
			$udata[] = trim($custname  );
			$udata[] = trim($custmail  );
			$udata[] = trim($custbday  );
			$udata[] = trim($compname  );
			$udata[] = trim($brandname  );
			$udata[] = trim($campaignname  );
			$udata[] = trim($brandname  );
			$udata[] = trim($channelname   );
			$udata[] = trim($ts        );  
			//fmt
			$str   = $utils->fmt_csv($udata);

			$utils->io_save($csv, str_replace("\n",'', $str)."\n",'a');
		}
		
		//give it back
		return array(
			'total' => $total,
			'fn'    => $fn
		);
	}


	
	/**
	 * Lists all models.
	 */
	public function actionPtslog($id)
	{
		
		//criteria
		$id       = addslashes($id);
		$criteria = new CDbCriteria;
		
		//PointsId
		if($id>0)
		{
			$criteria->addCondition(" (
			 	t.PointsId     = '$id' 
			 ) ");
		}			


		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN') {
			$dataProvider = new CActiveDataProvider('PointsLog', array(
				'criteria'=>$criteria ,
			));
		} else {
			$t = addslashes(trim(Yii::app()->user->ClientId)); 
			$criteria->addCondition(" (
			 	t.ClientId = '$t' 
			 ) ");
			$dataProvider = new CActiveDataProvider('PointsLog', array(
				'criteria'=>$criteria ,
			));
		}
		
		//exit;
		$this->render('ptslog',array(
			'dataProvider' => $dataProvider
		));
	}
	
	public function actionPtsloghistory()
	{
		
		//criteria
		$criteria   = new CDbCriteria;
		$filterSrch = 0;
		
		//channel-name
		$byChannel   = trim(Yii::app()->request->getParam('byChannel'));
		$ofilter     = '';
		if(strlen($byChannel))
		{
			$filterSrch++;
			$ofilter = " AND chan.ChannelName LIKE '%".addslashes($byChannel)."%' ";

		}
		//campaign
		$byCampaign   = trim(Yii::app()->request->getParam('byCampaign'));
		$pfilter     = '';
		if(strlen($byCampaign))
		{
			$filterSrch++;
			$pfilter = " AND camp.CampaignName LIKE '%".addslashes($byCampaign)."%' ";
		}
		//brand
		$byBrand   = trim(Yii::app()->request->getParam('byBrand'));
		$qfilter     = '';
		if(strlen($byBrand))
		{
			$filterSrch++;
			$qfilter = " AND brnd.BrandName LIKE '%".addslashes($byBrand)."%' ";
		}
		//customer
		$byClientName = trim(Yii::app()->request->getParam('byClientName'));
		$rfilter      = '';
		if(strlen($byClientName))
		{
			$filterSrch++;
			$rfilter = " AND (
						 clnt.CompanyName LIKE '%".addslashes($byClientName)."%'
				     ) ";
		}
		//date: 
		$byTranDateFr = trim(Yii::app()->request->getParam('byTranDateFr'));
		$dtfilter1     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateFr))
		{
			$filterSrch++;
			$t = addslashes($byTranDateFr);
			$dtfilter1 = " AND ( ptslog.DateCreated >= '$t 00:00:00' ) ";
		}
		//date: 
		$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
		$dtfilter2     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
		{
			$filterSrch++;
			$t = addslashes($byTranDateTo);
			$dtfilter2 = " AND ( ptslog.DateCreated <= '$t 23:59:59' ) ";
		}
				
		
		//no-filter
		$sfilter     = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$t       =  addslashes(Yii::app()->user->ClientId);
			$sfilter = " AND ptslog.ClientId = '$t' ";
		}


		//email + cust-name + etc
		if('sortby' == 'sortby')
		{
				// set sort options
				$sort = new CSort;
				$sort->attributes = array(
								'EmailAdd'       => array(
										'asc'    =>'cust.Email',
										'desc'   =>'cust.Email DESC',
										'label'  =>'Email',
								),
								'CustomerNm'  => array(
										'asc'    =>'cust.LastName',
										'desc'   =>'cust.LastName DESC',
										'label'  =>'Customer Name',
								),
								'*',
				);
				//$sort->multiSort  = true;
		}


	
		
		if(1){
		$rawSql   = "
			SELECT
				ptslog.PointLogId     ,
				ptslog.CustomerId     ,
				ptslog.SubscriptionId ,
				ptslog.ClientId       ,
				clnt.CompanyName      ,
				ptslog.BrandId        ,
				brnd.BrandName        ,
				ptslog.CampaignId     ,
				camp.CampaignName     ,
				ptslog.ChannelId      ,
				chan.ChannelName      ,
				ptslog.PointsId       ,
				pts.Name as PointsName,
				ptslog.ActiontypeId   ,
				act.Name as ActionTypeName,
				act.Value as ActionTypeValue,
				ptslog.LogType        ,
				ptslog.Value          ,
				ptslog.DateCreated    ,
				ptslog.CreatedBy      ,
				cust.BirthDate        ,
				cust.FirstName        ,
				cust.LastName         ,
				cust.Email            
			FROM
				points_log ptslog,
				customers cust,
				clients   clnt,
				brands    brnd,
				campaigns camp,
				channels  chan,
				action_type act,
				points pts,
				customer_subscriptions sub 
			WHERE
				    1=1
			        AND ptslog.CustomerId     = cust.CustomerId
			        AND ptslog.ClientId       = clnt.ClientId
			        AND ptslog.BrandId        = brnd.BrandId
			        AND ptslog.CampaignId     = camp.CampaignId
			        AND ptslog.ChannelId      = chan.ChannelId
			        AND ptslog.SubscriptionId = sub.SubscriptionId
			        AND ptslog.ActiontypeId   = act.ActiontypeId
			        AND ptslog.PointsId       = pts.PointsId
			        AND pts.PointsId          = act.PointsId
			$ofilter 
			$pfilter 
			$qfilter 
			$rfilter 
			$sfilter 
			$dtfilter1
			$dtfilter2
			
			ORDER BY ptslog.DateCreated DESC
			";
			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'PointLogId',
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
			);

		}

		//get csv
		//$csv = $this->formatCsvPtsloghistory($rawSql,null,null);
		
		//exit;
		$this->render('ptsloghistory',array(
			'dataProvider' => $dataProvider,
			'model'        => ReportsList::model(),
			//'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),	
		));
	}
	
	protected function formatCsvPtsloghistory($rawSql, $criteria, $sort)
	{
		$fn   = sprintf("%s-PointsLogHistory-%s-%s-%s.csv",Yii::app()->params['reportPfx'],@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//ensure
		if (!@file_exists(Yii::app()->params['reportCsv'])) {
		    @mkdir(Yii::app()->params['reportCsv'], 0777, true);
		}
		
		//fmt it her		
		$rawData       = Yii::app()->db->createCommand($rawSql); 
		$rawCount      = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dbprovider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'PointLogId',
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
		);

		//set
		$dbprovider->setPagination(false);
		$total = 0;
		
		//hdr
		$hdr_ttl = array("SEQ. NO.",
				 "CUSTOMER ID",
				 "CUSTOMER NAME",
				 "CUSTOMER EMAIL",
				 "CUSTOMER BDAY",
				 "COMPANY NAME",
				 "BRAND NAME",
				 "CAMPAIGN NAME",
				 "CHANNEL NAME",
				 "POINTS NAME",
				 "DATE CREATED",
			 );

		$utils = new Utils;
		$hdr   = $utils->fmt_csv($hdr_ttl);
		
		$utils->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		$total = 0;
		//get csv
		foreach($dbprovider->getData() as $row) 
		{
			
			$total++;
			//customer
			$custmail = $row["Email"];
			$custuid  = $row["CustomerId"];
			$custbday = $row["BirthDate"];
			$custname = $row["CustomerName"];
			
			//comp
			$compname  = $row["CompanyName"];

			
			//brand
			$brandname    = $row["BrandName"];
			$campaignname = $row["CampaignName"];
			$channelname  = $row["ChannelName"];
			
			//campaign
			$ptsname   = $row["PointsName"];
			

			//hdr
			$ts       = $row["DateCreated"];
						           

			//fmt
			$udata   = array();
			$udata[] = trim($total     );
			$udata[] = trim($custuid   );
			$udata[] = trim($custname  );
			$udata[] = trim($custmail  );
			$udata[] = trim($custbday  );
			$udata[] = trim($compname  );
			$udata[] = trim($brandname  );
			$udata[] = trim($campaignname  );
			$udata[] = trim($brandname  );
			$udata[] = trim($channelname   );
			$udata[] = trim($ts        );  
			//fmt
			$str   = $utils->fmt_csv($udata);

			$utils->io_save($csv, str_replace("\n",'', $str)."\n",'a');
		}
		
		//give it back
		return array(
			'total' => $total,
			'fn'    => $fn
		);
	}


	
	public function actionCusthistory($id=0)
	{
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		$cuid     = trim(Yii::app()->request->getParam('cuid'));
		//all-pending
		
		$clid   = addslashes(Yii::app()->user->ClientId);
		$xtra   = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$xtra   = " AND b.ClientId = '$clid'  ";
		}
		
		//overwrite
		if($cuid>0 and $id <= 0 ) 
			$id = $cuid;
		$vlid   = addslashes($id);
		$vxtra  = '';
		if($id > 0)
		{
			$vxtra   = " AND b.CustomerId = '$vlid'  ";
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
			$rawSql = "
				select IFNULL(b.Value,0) as TotalPoints, 
				       b.LogType,
					   (
					   select c.ChannelName
					   from channels c
						where  1=1
						and c.ClientId   = b.ClientId
						and c.BrandId    = b.BrandId
						and c.CampaignId = b.CampaignId
					   limit 1
					) as ChannelName,
					(
						select pts.Name
						from
						 points pts
						where
						pts.PointsId = b.PointsId
						limit 1	
					) as PointsSystemName,
					(
						select pts.DateCreated
						from
						 points_log pts
						where
							pts.PointsId = b.PointsId
						order by pts.DateCreated DESC
						limit 1	
					) as PointsSystemDate,
					(
					   select CONCAT(cust.FirstName,' ' ,cust.LastName)
					   from
						customers  cust
					   where
						cust.CustomerId = e.CustomerId
					   limit 1
					) as CustomerName,
					f.CompanyName,
					h.CampaignName,
					g.BrandName,
					f.ClientId,
					e.CustomerId,
					g.BrandId,
					h.CampaignId,
					b.PointsId,
					b.PointLogId
				from  points_log b,
				      customers e,
					  clients f,
					  brands g,
					  campaigns h
				WHERE 1=1
					and   b.CustomerId     = e.CustomerId
					and   b.ClientId       = f.ClientId
					and   b.BrandId        = g.BrandId
					and   b.CampaignId     = h.CampaignId
					$xtra
					$vxtra
					$filter
				ORDER BY b.DateCreated DESC
				";
		if(0)
		{
		echo "<hr><pre>$rawSql</pre><hr>";
		exit;
		}
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProvider    = new CSqlDataProvider($rawData, array(
					    'keyField'       => 'PointLogId',
					    'totalItemCount' => $rawCount,
					    )
			);
		
		}
		
		if(0){
    		
    			echo '<hr><hr>'.@var_export($rawSql,true);
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
		
		$this->render('custhistory',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
			
			
		));
	}

}
