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
		$byCustomerName   = trim(Yii::app()->request->getParam('byCustomerName'));
		$rfilter     = '';
		if(strlen($byCustomerName))
		{
			$filterSrch++;
			$rfilter = " AND (
						 cust.Email     LIKE '%".addslashes($byCustomerName)."%' OR
						 cust.FirstName LIKE '%".addslashes($byCustomerName)."%' 
				     ) ";
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


		$dataProvider = new CActiveDataProvider('Reports', array(
				'criteria'=> $criteria,
				
				));


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
				customer_subscriptions sub,
				action_type act,
				points pts
				
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
				 "CAMPAIGN NAME",
				 "CHANNEL NAME",
				 "CREATED BY",
				 "POINTS",
				 "POINTS-TYPE",
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
			$cmpgnname = $row["CampaignName"];

			//channel
			$chnlname  = $row["ChannelName"];

			//by
			$by        = $row["CreatedBy"];

			//pts
			$tpts      = $row["ActionTypeValue"];

			//hdr
			$typ       = $row["LogType"];
						           

			//fmt
			$udata   = array();
			$udata[] = trim($total     );
			$udata[] = trim($custuid   );
			$udata[] = trim($custname  );
			$udata[] = trim($custmail  );
			$udata[] = trim($custbday  );
			$udata[] = trim($compname  );
			$udata[] = trim($brandname );
			$udata[] = trim($cmpgnname );
			$udata[] = trim($chnlname  );
			$udata[] = trim($by        );
			$udata[] = trim($tpts      );   
			$udata[] = trim($typ       );  
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

			$this->render('pointsgainbal',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,


			));
	
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
			$xtra   = " AND sub.ClientId = '$clid'  ";
		}
		$filter = '';
		if(strlen($search)) 
		    $filter = " AND gen.Code LIKE '%".addslashes($search)."%' ";
		
		if(1){
		$rawSql   = "
			SELECT  DISTINCT 
				gen.GeneratedCouponId,
				gen.Code,
			        sub.PointsId,
				(
					select pts.Name
					from
					 points pts
					where
					pts.PointsId = sub.PointsId
					limit 1	
				) as PointsSystemName,
				sub.CustomerId,
				(
				   select CONCAT(cust.FirstName,' ' ,cust.LastName)
				   from
					customers  cust
				   where
					cust.CustomerId = sub.CustomerId
				   limit 1
				) as CustomerName,
				(
				   select cust.Email
				   from
					customers  cust
				   where
					cust.CustomerId = sub.CustomerId
				   limit 1
				) as Email,
				sub.ClientId,
				(
				   select clnt.CompanyName
				   from clients clnt
				   where
					clnt.ClientId = sub.ClientId
				   limit 1
				) as ClientName,
				(
				   select brnd.BrandName
				   from brands brnd
				   where
					brnd.BrandId = sub.BrandId
				   limit 1
				) as BrandName,
				(
				   select camp.CampaignName
				   from campaigns camp
				   where
					camp.CampaignId = sub.CampaignId
				   limit 1
				) as CampaignName,
				(
				   select chan.ChannelName
				   from channels chan
				   where
					chan.ClientId    = sub.ClientId
					and
					chan.BrandId     = sub.BrandId
					and
					chan.CampaignId  = sub.CampaignId
				   limit 1
				) as ChannelName,
				gen.CouponId,
				gen.DateRedeemed
			FROM 
				customer_subscriptions sub,
				coupon map,
				generated_coupons gen
			WHERE   1=1
				AND sub.PointsId   = map.PointsId
				AND sub.ClientId   = map.ClientId
				AND sub.Status     = 'ACTIVE'
				AND gen.Status     != 'PENDING'
				AND sub.PointsId   = gen.PointsId
				AND map.CouponId   = gen.CouponId
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
			$rawSql = "
				select a.CustomerId, 
				       a.SubscriptionId, 
				       a.ClientId, 
				       a.BrandId, 
				       a.CampaignId, 
				       a.status SubsriptionStatus,
				       b.Balance, 
				       b.Used, 
				       b.Total,
				       e.Email,
				       e.FirstName,
				       e.LastName,
				       f.CompanyName, g.BrandName, h.CampaignName,
				       (
					   select c.ChannelId
					   from channels c
						where  1=1
						and c.ClientId   = a.ClientId
						and c.BrandId    = a.BrandId
						and c.CampaignId = a.CampaignId
					   limit 1
				       ) as ChannelId,
				       (
					   select c.ChannelName
					   from channels c
						where  1=1
						and c.ClientId   = a.ClientId
						and c.BrandId    = a.BrandId
						and c.CampaignId = a.CampaignId
					   limit 1
					) as ChannelName
				from  customer_subscriptions a, 
				      customer_points b,
				      customers e,clients f,brands g,campaigns h
				WHERE 1=1
					and   a.SubscriptionId = b.SubscriptionId			
					and   a.CustomerId     = e.CustomerId
					and   a.ClientId       = f.ClientId
					and   a.BrandId        = g.BrandId
					and   a.CampaignId     = h.CampaignId
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
				$xtra   = " AND cust.ClientId = '$clid'  ";
			}
			
			$filter = '';
			if(strlen($search) > 0) 
			{
				$srch   = addslashes($search);
				$filter = " AND EXISTS (
							 select 1
							   from channels chan
							 where
								chan.ClientId    = cust.ClientId
								and
								chan.BrandId     = cust.BrandId
								and
								chan.CampaignId  = cust.CampaignId
								and
								chan.ChannelName LIKE '%$srch%'
							 limit 1
				                    ) ";
			}
			    
			
			if(1){
				$rawSql = "
					SELECT  DISTINCT
						cust.PointsId,
						cust.CustomerId ,
						cust.ClientId   ,
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
							Where pts.PointsId = cust.PointsId
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
							  dtls.PointsId = cust.PointsId
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
						   select custm.Email
						   from
							customers  custm
						   where
							custm.CustomerId = cust.CustomerId
						   limit 1
						) as Email,
						(
						   select brnd.BrandName
						   from brands brnd
						   where
							brnd.BrandId = cust.BrandId
						   limit 1
						) as BrandName,
						(
						   select camp.CampaignName
						   from campaigns camp
						   where
							camp.CampaignId = cust.CampaignId
						   limit 1
						) as CampaignName,
						(
						   select chan.ChannelName
						   from channels chan
						   where
							chan.ClientId    = cust.ClientId
							and
							chan.BrandId     = cust.BrandId
							and
							chan.CampaignId  = cust.CampaignId
						   limit 1
						) as ChannelName,
						rdm.RedeemedId
					FROM
						customer_subscriptions cust,
						rewards_list rlist,
						redeemed_reward rdm
					WHERE
					1=1
						AND cust.ClientId   = rlist.ClientId
						AND cust.ClientId   = rdm.ClientId
						AND rdm.UserId      = cust.CustomerId
						AND rlist.Status  IN ('ACTIVE')
						AND cust.Status   IN ('ACTIVE')
						AND rlist.RewardId  = rdm.RewardId
						AND rdm.PointsId    = cust.PointsId
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
							f.CampaignName  LIKE '%$srch%'   
				                 ) ";
			}
			    
			
			if(1){
				$rawSql = "
      					      
					SELECT b.CustomerId, h.Email, 
						b.BrandId, b.CampaignId, 
						f.CampaignName, g.CompanyName, 
						d.BrandName, f.Description, '' as ChannelName,
						b.Status
					FROM customer_subscriptions b
						join brands d on b.BrandId = d.BrandId
						join campaigns f on b.CampaignId = f.CampaignId
						join clients g on b.ClientId = g.ClientId
						join customers h on b.CustomerId = h.CustomerId
					WHERE 1=1 $xtra $filter 
						group by b.CustomerId, 
						b.BrandId, 
						b.CampaignId, 
						b.Status, 
						f.CampaignName, 
						g.CompanyName, 
						d.BrandName	,
						f.Description,
						h.Email      					      
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
