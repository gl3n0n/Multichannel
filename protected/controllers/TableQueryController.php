<?php

class TableQueryController extends Controller
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
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	
	/**
	* Lists all models.
	*/
	public function actionIndex()
	{
		//exit;
		$this->render('index',array(
			'Clients'      => Clients::model(),
			'Customers'    => Customers::model(),
			'Tables'       => $this->getAllTables(),
			'model'        => Clients::model(),
			'byTableNameX' => null,
			'showColumns'  => array(),
		));
	}
	/**
	* Lists all models.
	*/
	public function actionQuery()
	{
		
		//criteria
		$criteria = new CDbCriteria;
		$filterSrch     = 0;
		
		//date: 
		$byTranDateFr = trim(Yii::app()->request->getParam('byTranDateFr'));
		$dtfilter1     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateFr))
		{
			$filterSrch++;
			$t = addslashes($byTranDateFr);
			$dtfilter1 = " AND ( t.DateCreated >= '$t 00:00:00' ) ";
		}
		//date: 
		$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
		$dtfilter2     = '';
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
		{
			$filterSrch++;
			$t = addslashes($byTranDateTo);
			$dtfilter2 = " AND ( t.DateCreated <= '$t 23:59:59' ) ";
		}
		
		//byClientName
		$byClientName   = trim(Yii::app()->request->getParam('byClientName'));
		$filterX1       = '';
		if(strlen($byClientName))
		{
			$t = addslashes($byClientName);
			$filterX1  = " AND EXISTS (
									SELECT 1
									From clients cc
									Where 
										1=1
										AND cc.ClientId = t.ClientId
										AND cc.CompanyName LIKE '%$t%'
									LIMIT 1
							) ";
		}

		
		//table
		$dataProvider= null;
		$alltables   = $this->getAllTables();
		$byTableName = strtolower(trim(Yii::app()->request->getParam('byTableName')));
		$byTableNameX= '';
		
		if(!strlen($byTableName))
		{
			$this->actionIndex();
			return;
		}

		
		//more filters
		$allcols     = $this->getAllColumns();
		$dispcols    = $this->getSelectStar();
		$tableCols   = (! empty($_REQUEST['tableCols']))?($_REQUEST['tableCols']):(null);
		$tableFilters= @trim($_REQUEST['tableFilters']);
		$byFilterName= @trim($_REQUEST['byFilterName']);
		$selectAll   = " t.* ";
		$xtrs        = $allcols[$byTableName];
		$sel         = array();
		$showColumns = $dispcols[$byTableName];
		
		//filter string
		$xfilter_str = '';
		if(@array_key_exists($tableFilters, $xtrs ) and strlen($byFilterName)) 
		{
		    $s           = addslashes($byFilterName);
			$t           = addslashes($tableFilters);
			$xfilter_str = sprintf(" AND ( t.%s LIKE '%%%s%%' ) ", $t, $s);
		}
		
		if(@is_array($tableCols) and @is_array($xtrs))
		{
			$showColumns = array();
			switch($byTableName)
			{
					case 'customers':
						$sel[] = " t.CustomerId ";
						break;
					case 'users':
						$sel[] = " t.UserId ";
						break;
					case 'brands':
						$sel[] = " t.BrandId ";
						break;
					case 'campaigns':
						$sel[] = " t.CampaignId ";
						break;
					case 'channels':
						$sel[] = " t.ChannelId ";
						break;
					case 'points':
						$sel[] = " t.PointsId ";
						break;
					case 'points_mapping':
						$sel[] = " t.PointMappingId ";
						break;
					case 'action_type':
						$sel[] = " t.ActiontypeId ";
						break;
					case 'rewards_list':
						$sel[] = " t.RewardId ";
						break;
					case 'reward_details':
						$sel[] = " t.RewardConfigId ";
						break;
					case 'coupon':
						$sel[] = " t.CouponId ";
						break;
					case 'coupon_to_points':
						$sel[] = " t.CtpId ";
						break;
					case 'raffle':
						$sel[] = " t.RaffleId ";
						break;
						
					default;
						$sel[] = " t.ClientId ";
						break;
					
			}
			
			foreach($tableCols as $k => $v)
			{
				if(@array_key_exists($v, $xtrs )) 
				{
					$kv = sprintf(" t.%s ",$v);
					if(!@in_array($kv, $sel )) 
					{
						$sel[] = $kv;
					}
					$showColumns[] = $v;
				}
			}
			if(@count($sel))
			{
				$selectAll   = @join(',' , $sel);
			}
		}
	
		if(@array_key_exists($byTableName, $alltables)) 
		{
			$byTableNameX = $byTableName;		
			switch($byTableName)
			{
					case 'customers':
						$keyField  = "CustomerId";
					    $rawSql    = "
							SELECT $selectAll ,
							       clnt.CompanyName
							FROM
								customers t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'users':
						$keyField  = "UserId";
					    $rawSql    = "  
							SELECT $selectAll,
								clnt.CompanyName
							FROM
								users t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'brands':
						$keyField  = "BrandId";
					    $rawSql          = "
							SELECT $selectAll,
								clnt.CompanyName
							FROM
								brands t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'campaigns':
						$keyField  = "CampaignId";
					    $rawSql    = "  
							SELECT $selectAll,
								clnt.CompanyName
							FROM
								campaigns t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'channels':
						$keyField  = "ChannelId";
					    $rawSql    = " 
							SELECT $selectAll,
									clnt.CompanyName
							FROM
								channels t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField ,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'raffle':
						$keyField  = "RaffleId";
					    $rawSql    = " 
							SELECT $selectAll,
									clnt.CompanyName
							FROM
								raffle t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'coupon_to_points':
						$keyField  = "CtpId";
					    $rawSql    = " 
							SELECT $selectAll,
									clnt.CompanyName
							FROM
								coupon_to_points t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField ,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'coupon':
						$keyField  = "CouponId";
					    $rawSql    = " 
							SELECT $selectAll,
									clnt.CompanyName
							FROM
								coupon t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'reward_details':
						$keyField  = "RewardConfigId";
					    $rawSql    = " 
							SELECT $selectAll,
									clnt.CompanyName
							FROM
								reward_details t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'rewards_list':
						$keyField  = "RewardId";
					    $rawSql    = " 
							SELECT $selectAll,
									clnt.CompanyName
							FROM
								rewards_list t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'action_type':
					    $keyField  = "ActiontypeId";
						$rawSql    = " 
							SELECT $selectAll,
									clnt.CompanyName
							FROM
								action_type t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'points_mapping':
						$keyField  = "PointMappingId";
					    $rawSql          = " 
							SELECT $selectAll,
									clnt.CompanyName
							FROM
								points_mapping t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField ,
								'totalItemCount' => $rawCount,
								)
								);
						break;
					case 'points':
						$keyField  = "PointsId";
					    $rawSql          = " 
							SELECT $selectAll,
									clnt.CompanyName
							FROM
								points t,
								clients clnt
							WHERE
								1=1 and t.ClientId = clnt.ClientId
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						break;
						
						
					//clients
					default:
					    $keyField  = "ClientId";
						$rawSql    = "
							SELECT $selectAll
							FROM
								clients t
							WHERE
								1=1
								$dtfilter1
								$dtfilter2
								$filterX1
								$xfilter_str 
							ORDER BY t.DateCreated DESC
						";
						$rawData         = Yii::app()->db->createCommand($rawSql); 
						$rawCount        = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
						$dataProvider    = new CSqlDataProvider($rawData, array(
								'keyField'       => $keyField,
								'totalItemCount' => $rawCount,
								)
								);
						
						break;
			}
		}
		
		//$selectAll   = " t.* ";
		if(0)
		{
			echo "<hr><pre>".@var_export($_REQUEST,1)   ."###$selectAll</pre><hr>";
			echo "<hr><pre>".@var_export($showColumns,1)."###$rawSql</pre><hr>";
			exit;
			//$this->formatCsv($showColumns,$keyField, $rawSql, $criteria, $sort)
		}		
		
		
		
		//make csv
		$csv      = $this->formatCsv($showColumns,$keyField, $rawSql, null, null);
		
		//exit;
		$this->render('index',array(
			'Clients'      => Clients::model(),
			'Customers'    => Customers::model(),
			'Tables'       => $this->getAllTables(),
			'model'        => Clients::model(),
			'dataProvider' => $dataProvider,
			'byTableNameX' => $byTableNameX,
			'showColumns'  => $showColumns,
			'downloadCSV'  => (@intval($csv['total'])>0)?($csv['fn']):(''),
		));
	}
	
	public function getAllTables()
	{
		return array(
			'clients'   => 'Clients',
			'customers' => 'Customers',
			'brands'    => 'Brands',
			'campaigns' => 'Campaigns',
			'channels'  => 'Channels',
			'users'     => 'Users',
			'points'            => 'Points System',
			'points_mapping'    => 'Points System Mapping',
			'action_type'       => 'Action Type',
			'rewards_list'      => 'Rewards List',
			'reward_details'    => 'Rewards and Redemption',
			'coupon'            => 'Coupon System',
			'coupon_to_points'  => 'Coupon To Points',
			'raffle'            => 'Raffles',
		);
	}
	
	//all that can be show as selected
	public function getAllColumns()
	{

		return array(
				'clients' => array(
					'ClientId'    => 'ClientId'   ,
					'CompanyName' => 'CompanyName',
					'Address'     => 'Address'    ,
					'Email'       => 'Email'      ,
					'Landline'    => 'Landline'   ,
					'Status'      => 'Status'     ,
					'DateCreated' => 'DateCreated',
					'CreatedBy'   => 'CreatedBy',
					'DateUpdated' => 'DateUpdated',
					'UpdatedBy'   => 'UpdatedBy',	
					),    
				'customers' => array(
					'CustomerId'    => 'CustomerId'    ,
					'FirstName'     => 'FirstName'     ,
					'MiddleName'    => 'MiddleName'    ,
					'LastName'      => 'LastName'      ,
					'Gender'        => 'Gender'        ,
					'ContactNumber' => 'ContactNumber' ,
					'Email'         => 'Email'         ,
					'Status'        => 'Status'        ,
					'DateCreated' => 'DateCreated',
					'CreatedBy'   => 'CreatedBy',
					'DateUpdated' => 'DateUpdated',
					'UpdatedBy'   => 'UpdatedBy',

					),
				'users' => array(
						'UserId'        => 'UserId'        ,
						'FirstName'     => 'FirstName'     ,
						'LastName'      => 'LastName'      ,
						'Gender'        => 'Gender'        ,
						'Birthdate'     => 'Birthdate'     ,
						'Address'       => 'Address'       ,
						'Email'         => 'Email'         ,
						'Username'      => 'Username'      ,
						'AccessType'    => 'AccessType'    ,
						'Status'        => 'Status'        ,
						'DateCreated' => 'DateCreated',
						'CreatedBy'   => 'CreatedBy',
						'DateUpdated' => 'DateUpdated',
						'UpdatedBy'   => 'UpdatedBy',

						),
				'brands' => array(
						'BrandId'      => 'BrandId'    ,
						'BrandName'    => 'BrandName'    ,
						'Description'  => 'Description'  ,
						'DurationFrom' => 'DurationFrom' ,
						'DurationTo'   => 'DurationTo'   ,
						'Status'       => 'Status'       ,
						'DateCreated' => 'DateCreated',
						'CreatedBy'   => 'CreatedBy',
						'DateUpdated' => 'DateUpdated',
						'UpdatedBy'   => 'UpdatedBy',

						),			
				'campaigns' => array(
						'CampaignId'   => 'CampaignId'   ,
						'CampaignName' => 'CampaignName' ,
						'Description'  => 'Description'  ,
						'DurationFrom' => 'DurationFrom' ,
						'DurationTo'   => 'DurationTo'   ,
						'Type'         => 'Type'         ,
						'Status'       => 'Status'       ,
						'DateCreated' => 'DateCreated',
						'CreatedBy'   => 'CreatedBy',
						'DateUpdated' => 'DateUpdated',
						'UpdatedBy'   => 'UpdatedBy',

						),
				'channels' => array(
						'ChannelId'    => 'ChannelId'   ,
						'ChannelName'  => 'ChannelName' ,
						'Description'  => 'Description' ,
						'DurationFrom' => 'DurationFrom',
						'DurationTo'   => 'DurationTo'  ,
						'Type'         => 'Type'        ,
						'Status'       => 'Status'      ,
						'DateCreated' => 'DateCreated',
						'CreatedBy'   => 'CreatedBy',
						'DateUpdated' => 'DateUpdated',
						'UpdatedBy'   => 'UpdatedBy',
						),       
				'points'            => array( 
						'PointsId'    => 'PointsId'   ,
						'Name'        => 'Name'       ,
						'Status'      => 'Status'     ,
						'DateCreated' => 'DateCreated',
						'CreatedBy'   => 'CreatedBy'  ,
						'DateUpdated' => 'DateUpdated',
						'UpdatedBy'   => 'UpdatedBy'  ,
						),
				'points_mapping'       => array(
						'PointMappingId' => 'PointMappingId',
						'PointsId'       => 'PointsId'      ,
						'BrandId'        => 'BrandId'       ,
						'CampaignId'     => 'CampaignId'    ,
						'ChannelId'      => 'ChannelId'     ,
						'Status'         => 'Status'        ,
						'DateCreated'    => 'DateCreated'   ,
						'CreatedBy'      => 'CreatedBy'     ,
						'DateUpdated'    => 'DateUpdated'   ,
						'UpdatedBy'      => 'UpdatedBy'     ,
						),
				'action_type'          => array(
						'ActiontypeId'   => 'ActiontypeId'  ,
						'Name'           => 'Name'          ,
						'PointsId'       => 'PointsId'      ,
						'Value'          => 'Value'         ,
						'PointsAction'   => 'PointsAction'  ,
						'PointsCapping'  => 'PointsCapping' ,
						'PointsLimit'    => 'PointsLimit'   ,
						'StartDate'      => 'StartDate'     ,
						'EndDate'        => 'EndDate'       ,
						'Status'         => 'Status'        ,
						'DateCreated'    => 'DateCreated'   ,
						'CreatedBy'      => 'CreatedBy'     ,
						'DateUpdated'    => 'DateUpdated'   ,
						'UpdatedBy'      => 'UpdatedBy'     ,
						),
				'rewards_list'      => array(
						'RewardId'     => 'RewardId'     ,
						'BrandId'      => 'BrandId'      ,
						'CampaignId'   => 'CampaignId'   ,
						'ChannelId'    => 'ChannelId'    ,
						'DateFrom'     => 'DateFrom'     ,
						'DateTo'       => 'DateTo'       ,
						'Title'        => 'Title'        ,
						'Description'  => 'Description'  ,
						'Image'        => 'Image'        ,
						'Availability' => 'Availability' ,
						'Status'       => 'Status'       ,
						'DateCreated'  => 'DateCreated'  ,
						'CreatedBy'    => 'CreatedBy'    ,
						'DateUpdated'  => 'DateUpdated'  ,
						'UpdatedBy'    => 'UpdatedBy'    ,
						'Value'        => 'Value'        ,
						),
				'reward_details'     => array(
						'RewardConfigId' => 'RewardConfigId' ,
						'RewardId'       => 'RewardId'       ,
						'PointsId'       => 'PointsId'       ,
						'Name'           => 'Name'           ,
						'Inventory'      => 'Inventory'      ,
						'Limitations'    => 'Limitations'    ,
						'Value'          => 'Value'          ,
						'StartDate'      => 'StartDate'      ,
						'EndDate'        => 'EndDate'        ,
						'Status'         => 'Status'         ,
						'DateCreated'    => 'DateCreated'    ,
						'CreatedBy'      => 'CreatedBy'      ,
						'DateUpdated'    => 'DateUpdated'    ,
						'UpdatedBy'      => 'UpdatedBy'      ,
						),
				'coupon'            => array(
						'CouponId'      => 'CouponId'     ,
						'PointsId'      => 'PointsId'     ,
						'Code'          => 'Code'         ,
						'CouponName'    => 'CouponName'   ,
						'Type'          => 'Type'         ,
						'TypeId'        => 'TypeId'       ,
						'Source'        => 'Source'       ,
						'ExpiryDate'    => 'ExpiryDate'   ,
						'CodeLength'    => 'CodeLength'   ,
						'CouponType'    => 'CouponType'   ,
						'PointsValue'   => 'PointsValue'  ,
						'Status'        => 'Status'       ,
						'Image'         => 'Image'        ,
						'Quantity'      => 'Quantity'     ,
						'LimitPerUser'  => 'LimitPerUser' ,
						'File'          => 'File'         ,
						'ImagePath'     => 'ImagePath'    ,
						'CouponUrl'     => 'CouponUrl'    ,
						'edit_flag'     => 'edit_flag'    ,
						'DateCreated'   => 'DateCreated'  ,
						'CreatedBy'     => 'CreatedBy'    ,
						'DateUpdated'   => 'DateUpdated'  ,
						'UpdatedBy'     => 'UpdatedBy'    ,
						),
						'coupon_to_points'  => array(
								'CtpId'         => 'CtpId'      ,
								'CouponId'      => 'CouponId'   ,
								'Name'          => 'Name'       ,
								'Value'         => 'Value'      ,
								'StartDate'     => 'StartDate'  ,
								'EndDate'       => 'EndDate'    ,
								'Status'        => 'Status'     ,
								'DateCreated'   => 'DateCreated',
								'CreatedBy'     => 'CreatedBy'  ,
								'DateUpdated'   => 'DateUpdated',
								'UpdatedBy'     => 'UpdatedBy'  ,
								),
						'raffle'            => array(
								'RaffleId'     => 'RaffleId'   ,
								'ClientId'     => 'ClientId'   ,
								'RaffleName'   => 'RaffleName' ,
								'Source'       => 'Source'     ,
								'NoOfWinners'  => 'NoOfWinners',
								'BackUp'       => 'BackUp'     ,
								'FdaNo'        => 'FdaNo'      ,
								'DrawDate'     => 'DrawDate'   ,
								'DateCreated'  => 'DateCreated',
								'CreatedBy'    => 'CreatedBy'  ,
								'DateUpdated'  => 'DateUpdated',
								'UpdatedBy'    => 'UpdatedBy'  ,
								'Status'       => 'Status'     ,
								'CouponId'     => 'CouponId'   ,
								),             

						);
	}
	
	
	public function getSelectStar()
	{
		$selectStar = array(
				'clients'=>array(
					'ClientId'    ,
					'CompanyName' ,
					'Address'     ,
					'Email'       ,
					'Landline'    ,
					'Status'      ,
					'DateCreated' ,
					'CreatedBy'   ,
					'DateUpdated' ,
					'UpdatedBy'   ,
					),
				'customers'=>array(
					'CustomerId'    ,
					'FirstName'     ,
					'MiddleName'    ,
					'LastName'      ,
					'Gender'        ,
					'ContactNumber' ,
					'Email'         ,
					'Status'        ,
					'DateCreated'   ,
					'CreatedBy'   ,
					'DateUpdated' ,
					'UpdatedBy'   ,
					'CompanyName'   ,   
					'BirthDate'     ,
					),
				'users'=>array(
						'UserId'        ,
						'FirstName'     ,
						'LastName'      ,
						'Gender'        ,
						'Birthdate'     ,
						'Address'       ,
						'Email'         ,
						'Username'      ,
						'AccessType'    ,
						'Status'        ,
						'DateCreated'   ,
						'CreatedBy'   ,
						'DateUpdated' ,
						'UpdatedBy'   ,
						'CompanyName'   ,
					      ),
				'brands'=>array(
						'BrandId'      ,
						'BrandName'    ,
						'Description'  ,
						'DurationFrom' ,
						'DurationTo'   ,
						'Status'       ,
						'DateCreated'  ,
						'CreatedBy'   ,
						'DateUpdated' ,
						'UpdatedBy'   ,
						'CompanyName'  ,
					       ),
				'campaigns'=>array(
						'CampaignId'   ,
						'CampaignName' ,
						'Description'  ,
						'DurationFrom' ,
						'DurationTo'   ,
						'Type',
						'Status'       ,
						'DateCreated'  ,
						'CreatedBy'   ,
						'DateUpdated' ,
						'UpdatedBy'   ,
						'CompanyName'  ,
						'BrandId',
						),
				'channels'=>array(
						'ChannelId'   ,
						'ChannelName' ,
						'Description'  ,
						'DurationFrom' ,
						'DurationTo'   ,
						'Type',
						'Status'       ,
						'DateCreated'  ,
						'CreatedBy'   ,
						'DateUpdated' ,
						'UpdatedBy'   ,
						'CompanyName'  ,
						'BrandId',
						'CampaignId',
						),
				'points'            => array( 'PointsId'   ,
						'Name'       ,
						'Status'     ,
						'DateCreated',
						'CreatedBy'  ,
						'DateUpdated',
						'UpdatedBy'  ,
						),
				'points_mapping'    => array('PointMappingId' ,
						'PointsId'       ,
						'BrandId'        ,
						'CampaignId'     ,
						'ChannelId'      ,
						'Status'         ,
						'DateCreated'    ,
						'CreatedBy'      ,
						'DateUpdated'    ,
						'UpdatedBy'      ,
						),
				'action_type'       => array('ActiontypeId'  ,
						'Name'          ,
						'PointsId'      ,
						'Value'         ,
						'PointsAction'  ,
						'PointsCapping' ,
						'PointsLimit'   ,
						'StartDate'     ,
						'EndDate'       ,
						'Status'        ,
						'DateCreated'   ,
						'CreatedBy'     ,
						'DateUpdated'   ,
						'UpdatedBy'     ,
						),
				'rewards_list'      => array('RewardId'     ,
						'BrandId'      ,
						'CampaignId'   ,
						'ChannelId'    ,
						'DateFrom'     ,
						'DateTo'       ,
						'Title'        ,
						'Description'  ,
						'Image'        ,
						'Availability' ,
						'Status'       ,
						'DateCreated'  ,
						'CreatedBy'    ,
						'DateUpdated'  ,
						'UpdatedBy'    ,
						'Value'        ,
						),
				'reward_details'    => array('RewardConfigId' ,
						'RewardId'       ,
						'PointsId'       ,
						'Name'           ,
						'Inventory'      ,
						'Limitations'    ,
						'Value'          ,
						'StartDate'      ,
						'EndDate'        ,
						'Status'         ,
						'DateCreated'    ,
						'CreatedBy'      ,
						'DateUpdated'    ,
						'UpdatedBy'      ,
						),
				'coupon'            => array('CouponId'     ,
						'PointsId'     ,
						'Code'         ,
						'CouponName'   ,
						'Type'         ,
						'TypeId'       ,
						'Source'       ,
						'ExpiryDate'   ,
						'CodeLength'   ,
						'CouponType'   ,
						'PointsValue'  ,
						'Status'       ,
						'Image'        ,
						'Quantity'     ,
						'LimitPerUser' ,
						'File'         ,
						'ImagePath'    ,
						'CouponUrl'    ,
						'edit_flag'    ,
						'DateCreated'  ,
						'CreatedBy'    ,
						'DateUpdated'  ,
						'UpdatedBy'    ,
						),
						'coupon_to_points'  => array('CtpId'       ,
								'CouponId'    ,
								'Name'        ,
								'Value'       ,
								'StartDate'   ,
								'EndDate'     ,
								'Status'      ,
								'DateCreated' ,
								'CreatedBy'   ,
								'DateUpdated' ,
								'UpdatedBy'   ,
								),
						'raffle'            => array('RaffleId'    ,
								'ClientId'    ,
								'RaffleName'  ,
								'Source'      ,
								'NoOfWinners' ,
								'BackUp'      ,
								'FdaNo'       ,
								'DrawDate'    ,
								'DateCreated' ,
								'CreatedBy'   ,
								'DateUpdated' ,
								'UpdatedBy'   ,
								'Status'      ,
								'CouponId'    ,
								),											
						);
		//give it back
		return $selectStar;
	}
	public function actionGetcolumns($colname='')
	{
		$colname = strtolower(trim(Yii::app()->request->getParam('colname')));
		$all     = $this->getAllColumns();
		//give
		$list = (@array_key_exists($colname, $all)) ? $all[$colname] : array();
		Yii::app()->utils->sendJSONResponse($list);
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
		$model = TableQuery::model()->findByPk($id);
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

	
	protected function formatCsv($showColumns,$keyField, $rawSql, $criteria, $sort)
	{
		$fn   = sprintf("%s-TableQry-%s-%s-%s.csv",Yii::app()->params['reportPfx'],@date("YmdHis"),uniqid(),md5(uniqid()));
		$csv  = Yii::app()->params['reportCsv'].DIRECTORY_SEPARATOR."$fn";
		
		//ensure
		if (!@file_exists(Yii::app()->params['reportCsv'])) {
		    @mkdir(Yii::app()->params['reportCsv'], 0777, true);
		}
		
		//fmt it her		
		$rawData       = Yii::app()->db->createCommand($rawSql); 
		$rawCount      = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dbprovider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => "$keyField",
				    'totalItemCount' => $rawCount,
				    'sort'           => $sort,
				    )
		);

		//set
		$dbprovider->setPagination(false);
		$total = 0;
		
		//hdr
		$hdr_ttl = $showColumns;

		$utils = new Utils;
		$hdr   = $utils->fmt_csv($hdr_ttl);
		
		$utils->io_save($csv, str_replace("\n",'', $hdr)."\n",'a');
		$total = 0;
		
		//get csv
		foreach($dbprovider->getData() as $row) 
		{
			
			$total++;
			
			//fmt
			$udata   = array();
			foreach($showColumns as $col)
			{
			$udata[] = trim($row["$col"]);
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



}
