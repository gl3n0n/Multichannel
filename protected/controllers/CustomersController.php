<?php

class CustomersController extends Controller
{
	public $extraJS;
	public $mainDivClass;
	public $modals;
	public $errorMessage;
	
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
				'actions'=>array('index','view','update','addsub'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','addsub'),
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
		$model = $this->loadModel($id);
		$total = $this->getSummaryPts(($model != null?$model->CustomerId:0));
		$this->render('view',array(
			'model'=> $model,
			'total'=> @intval($total),
		));
	}
	
	protected function getSummaryPts($custId=0)
	{
		$rawCount = 0;
		

		$more = '';
		if(Yii::app()->user->AccessType !== "SUPERADMIN" ) {
			$cid  = addslashes(Yii::app()->user->ClientId);
			$more = " and a.ClientId = '$cid' ";
			
		}
		
		if(1){
		$rawSql   = "
			select sum(Points) from (
			select a.CustomerId, a.SubscriptionId, a.ClientId, a.BrandId, a.CampaignId, a.status SubsriptionStatus,
			       b.Balance, b.Used, b.Total,
			       c.PointsId, c.Value Points
			from  customer_subscriptions a, customer_points b, points_log c
			where a.CustomerId     = '$custId'
			and   a.SubscriptionId = b.SubscriptionId
			and   a.SubscriptionId = c.SubscriptionId
			and   a.CustomerId     = c.CustomerId     $more
			union all
			select a.CustomerId, a.SubscriptionId, a.ClientId, a.BrandId, a.CampaignId, a.status SubsriptionStatus,
			       b.Balance, b.Used, b.Total,
			       ifnull(c.PointsId,0), c.Value Points
			from  customer_subscriptions a, customer_points b, points_log c
			where a.CustomerId     = '$custId'
			and   a.SubscriptionId = b.SubscriptionId
			and   a.SubscriptionId = c.SubscriptionId
			and   a.CustomerId     = c.CustomerId
			and   (c.PointsId = 0 or c.PointsId is null) $more
			) as count_alias
		";
		$rawCount = Yii::app()->db->createCommand(" $rawSql ")->queryScalar(); //the count
		
		}

		//give it back
		return $rawCount;
	
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Customers;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Customers']))
		{
			$model->attributes=$_POST['Customers'];
			
			if(Yii::app()->user->AccessType !== "SUPERADMIN" ) {
				$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			}
			
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->CustomerId));
			}
		}

		$this->render('create',array(
			'model'=>$model,
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

		if(isset($_POST['Customers']))
		{
			$model->attributes=$_POST['Customers'];
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->CustomerId));
			}
		}

		$this->render('update',array(
			'model'=>$model,
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
		$criteria = new CDbCriteria;
	
		//byCustomerName
		$byCustomerName   = trim(Yii::app()->request->getParam('byCustomerName'));
		if(strlen($byCustomerName))
		{
		    $t = addslashes($byCustomerName);
			$criteria->addCondition(" ( t.FirstName  LIKE '%$t%' OR t.LastName  LIKE '%$t%' ) ");
		}			
		//byEmail
		$byEmail   = trim(Yii::app()->request->getParam('byEmail'));
		if(strlen($byEmail))
		{
		    $t = addslashes($byEmail);
			$criteria->addCondition(" ( t.Email  LIKE '%$t%' ) ");
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

		//byCreatedDateFr: 
		$byCreatedDateFr = trim(Yii::app()->request->getParam('byCreatedDateFr'));
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byCreatedDateFr))
		{
			$t = addslashes($byCreatedDateFr);
			$criteria->addCondition(" ( t.DateCreated >= '$t 00:00:00' ) ");
		}
		//byCreatedDateTo: 
		$byCreatedDateTo = trim(Yii::app()->request->getParam('byCreatedDateTo'));
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byCreatedDateTo))
		{
			$t = addslashes($byCreatedDateTo);
			$criteria->addCondition(" ( t.DateCreated <= '$t 23:59:59' ) ");
		}		


		//byBirthDateFr: 
		$byBirthDateFr = trim(Yii::app()->request->getParam('byBirthDateFr'));
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byBirthDateFr))
		{
			$t = addslashes($byBirthDateFr);
			$criteria->addCondition(" ( t.BirthDate >= '$t 00:00:00' ) ");
		}
		//byBirthDateTo: 
		$byBirthDateTo = trim(Yii::app()->request->getParam('byBirthDateTo'));
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byBirthDateTo))
		{
			$t = addslashes($byBirthDateTo);
			$criteria->addCondition(" ( t.BirthDate <= '$t 23:59:59' ) ");
		}		

		
		//normal
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') 
		{
			$t = addslashes(Yii::app()->user->ClientId);
			$criteria->addCondition(" (  t.ClientId = '$t' )  ");
		}

		$dataProvider = new CActiveDataProvider('Customers', array(
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
		$model=new Customers('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Customers']))
			$model->attributes=$_GET['Customers'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Customers the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Customers::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Customers $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='customers-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionAddSub($id)
	{
		$xmore = '';
		$ymore = '';


		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') 
		{
			$cid   = addslashes(Yii::app()->user->ClientId); 
			$xmore = " AND t.ClientId = '$cid' ";
		}		
		if($id > 0)
		{
			$cid   = addslashes($id); 
			$ymore = " AND t.CustomerId = '$cid' ";
		}

		//chk
		$model = new CustomerSubscriptions;
		if(isset($_POST['CustomerSubscriptions']))
		{
			$model->attributes=$_POST['CustomerSubscriptions'];
			
			if(Yii::app()->user->AccessType !== "SUPERADMIN" ) {
				$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			}
			
			if(0)
			{
				echo "<pre>$id=OKS# " .@var_export($_POST,1)."</pre><hr>";
				echo "<pre>$id=OKS# " .@var_export($model,1)."</pre><hr>";
				exit;
			}
			
			//add-claim
			$modeType = 'ADD';
			if(!empty($_POST['btnSub']))
			{
				$modeType = 'CLAIM';
			}
			
			//pre-checking
			$CustomerId = trim($_POST['CustomerSubscriptions']['CustomerId']);
			if($CustomerId <= 0 )
				$model->addError('CustomerId', 'CustomerId cannot be blank.');
			
			$BrandId    = trim($_POST['CustomerSubscriptions']['BrandId']);
			if($BrandId <= 0 )
				$model->addError('BrandId',  'Brand cannot be blank.');
			
			$CampaignId = trim($_POST['CustomerSubscriptions']['CampaignId']);
			if($CampaignId <= 0 )
				$model->addError('CampaignId', 'Campaign cannot be blank.');
			
			$PointsIds = trim($_POST['CustomerSubscriptions']['PointsId']);
			if(!@preg_match("/^[0-9]+\-[0-9]+$/i",$PointsIds)) 
				$model->addError('PointsId', 'PointsId cannot be blank.');
			
			$PointsValue = trim($_POST['CustomerSubscriptions']['PointsValue']);
			if( $PointsValue <= 0 or ! @preg_match("/^\d{1,}$/", $PointsValue)  )
				$model->addError('PointsValue', 'Value cannot be blank/must be an integer.aaaa>'.$PointsValue);
				

			//chk if exists
			$ClientId = 0;
			$cid      = addslashes($CustomerId);
			$cusm     = Customers::model()->findAll(array(
				'select'    => '*', 
				'condition' => " CustomerId = '$cid'  "
			));
			if($cusm and @count($cusm)>0)
			{
				$ClientId = $cusm[0]->ClientId;
			}
			//normal-user
			if(Yii::app()->user->AccessType !== "SUPERADMIN" ) {
				//not-sync-CLIENT-ID per CUSTOMER-ID
				if($ClientId !=  Yii::app()->user->ClientId)
					$ClientId = 0;
			}
			if($ClientId <= 0 )
				$model->addError('ClientId', 'ClientId cannot be blank.');





			if(!$model->hasErrors())
			{	
				//fmt
				list($ActionTypeId,$PointsId ) = @explode('-',$PointsIds);
				
				//double-chk
				if($ActionTypeId <= 0 or $PointsId <= 0 ) 
					$model->addError('PointsId', 'PointsId cannot be blank.');
				//fmt
				$BrandId     = trim($_POST['CustomerSubscriptions']['BrandId']);
				$CampaignId  = trim($_POST['CustomerSubscriptions']['CampaignId']);
				$sqlwhere    = ' ';
				//client
				$cid       = addslashes($ClientId); 
				$sqlwhere .=  " AND t.ClientId  = '$cid' ";
				//customer
				$cid       = addslashes($CustomerId);
				$sqlwhere .=  " AND t.CustomerId = '$cid' ";
				//brand
				$cid       = addslashes($BrandId);
				$sqlwhere .=  " AND t.BrandId    = '$cid' ";
				//campaign
				$cid       = addslashes($CampaignId);
				$sqlwhere .=  " AND t.CampaignId = '$cid' ";
				//pointsid				
				$cid       = addslashes($PointsId);
				$sqlwhere .=  " AND t.PointsId   = '$cid' ";
				

				//echo " $sqlwhere; ";exit;
				//chk if exists
				$cust_subs = CustomerSubscriptions::model()->findAll(array(
						'select'    => '*', 
						'condition' => " Status='ACTIVE' $sqlwhere "
						));
			
				//init
				$pts_bal        = 0;
				$cust_pts       = null;
				$SubscriptionId = 0;
				$CustomerPointId= 0;
				$is_cust_pts    = 0;
				
				

				//chk points
				if($cust_subs and @count($cust_subs)>0)
				{
					
					//get subscriptionid
					$SubscriptionId = $cust_subs[0]->SubscriptionId;
					$model = CustomerSubscriptions::model()->findByPk($SubscriptionId);
					
					
					$sqlwhere  = ' 1=1 ';
					//subscriptionid				
					$cid       = addslashes($SubscriptionId);
					$sqlwhere .=  " AND t.SubscriptionId   = '$cid' ";
					//pointsid				
					$cid       = addslashes($PointsId);
					$sqlwhere .=  " AND t.PointsId   = '$cid' ";					
					
					//chk if exists
					$cust_pts = CustomerPoints::model()->findAll(array(
						'select'    => '*', 
						'condition' => " $sqlwhere "
					));

					//chk balance
					if($cust_pts and @count($cust_pts)>0)
					{
						$pts_bal         = $cust_pts[0]->Balance;
						$CustomerPointId = $cust_pts[0]->CustomerPointId;
						$is_cust_pts++;
					}
				}
				
				
				//deduct
				if( 
				    ( @preg_match("/^(CLAIM)$/i",$modeType) and $pts_bal     <= $PointsValue ) or
				    ( @preg_match("/^(CLAIM)$/i",$modeType) and $pts_bal     <= 0 ) or
				    ( @preg_match("/^(CLAIM)$/i",$modeType) and $is_cust_pts <= 0 ) 
				  )
				{

					if(0)
					{
						echo "<pre>$id=OKS#client=$ClientId# " .@var_export($_POST,1)."</pre><hr>";
						echo "<pre>$id=OKS#$modeType# " .@var_export($cust_subs,1)."</pre><hr>";
						echo "<pre>$id=OKS#$modeType# " .@var_export($cust_pts,1)."</pre><hr>";

					}
					$model->addError('PointsValue',  'Balance is less than the PointsValue');
				}
				
				//save
				$model->attributes=$_POST['CustomerSubscriptions'];
				$model->setAttribute("Status",     'ACTIVE');
				$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
				$model->setAttribute("CreatedBy",   Yii::app()->user->id);
				$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
				$model->setAttribute("UpdatedBy",   Yii::app()->user->id);
				$model->setAttribute("ClientId",    $ClientId);
				$model->setAttribute("PointsId",    $PointsId);

				//chk it again
				if(!$model->hasErrors())
				{
					if($model->save())
					{	
					     $SubscriptionId = $model->primaryKey;	
					     $pmodel = new CustomerPoints;
					     

					     //add customer_points
			     		     if($CustomerPointId <= 0)
			     		     {
	     					//new
						$pmodel->setAttribute("DateCreated", new CDbExpression('NOW()'));
						
	     					$pmodel->setAttribute("Balance",     $PointsValue);
			     		     }
			     		     else
			     		     {
			     		     	//reload
			     			$pmodel = CustomerPoints::model()->findByPk($CustomerPointId);     	
			     		     	//update
						$pmodel->setAttribute("DateUpdated", new CDbExpression('NOW()'));
						
	     					
			     		     }
			     		     
					     //deduct
					     if( @preg_match("/^(CLAIM)$/i",$modeType) )
					     {
						   $pmodel->setAttribute("Balance",  new CDbExpression("Balance - $PointsValue"));
						   $pmodel->setAttribute("Used",     new CDbExpression("Used + $PointsValue"));
					     }
					     else
					     {
						   $pmodel->setAttribute("Total",    new CDbExpression("Total + $PointsValue"));
						   $pmodel->setAttribute("Balance",  new CDbExpression("Balance + $PointsValue"));
					     }

					     $pmodel->setAttribute("UpdatedBy",   Yii::app()->user->id);
					     $pmodel->setAttribute("CreatedBy",   Yii::app()->user->id);
					     $pmodel->setAttribute("PointsId",      $PointsId);
					     $pmodel->setAttribute("SubscriptionId",$SubscriptionId);

					     if(0)
					     {
							echo "<pre>$id=OKS# " .@var_export($_POST,1)."</pre><hr>";
							echo "<pre>$id=OKS#$modeType# " .@var_export($pmodel,1)."</pre><hr>";

					     }


			     		     //pts
			     		     if(!$pmodel->save())
			     		     {
			     		     	     //oops
			     		     	     $this->errorMessage = 'Add/Deduct Points failed.';
						     $pmodel->addError('error', $this->errorMessage);
			     		     }
			     		     else
			     		     {
						     //good	
						     Yii::app()->user->setFlash('success', 'Add/Deduct Points is successful.');


						    //get it
						    $cmodel = Channels::model()->findAll(array(
									'select'    => 'ChannelId',  
									'condition' => " BrandId = '$BrandId' AND CampaignId = '$CampaignId' AND ClientId = '$ClientId'"
									));
						     
						     $ChannelId = (($cmodel and @count($cmodel)>0)) ? ($cmodel[0]->ChannelId) : (0);
						     
						     //pts log
						     $vmodel = new PointsLog;
						     $vmodel->setAttribute('CustomerId',$CustomerId);     
						     $vmodel->setAttribute('SubscriptionId',$SubscriptionId); 
						     $vmodel->setAttribute('ClientId',  $ClientId);       
						     $vmodel->setAttribute('BrandId',   $BrandId);        
						     $vmodel->setAttribute('CampaignId',$CampaignId);     
						     $vmodel->setAttribute('ChannelId', $ChannelId);      
						     $vmodel->setAttribute('PointsId',  $PointsId);       
						     $vmodel->setAttribute('ActiontypeId',$ActionTypeId);   
						     $vmodel->setAttribute('LogType',"MANUAL-$modeType");        
						     $vmodel->setAttribute('Value',     ((( @preg_match("/^(CLAIM)$/i",$modeType) ))?("-$PointsValue"):($PointsValue)));          
						     $vmodel->setAttribute("DateCreated", new CDbExpression('NOW()'));
						     $vmodel->setAttribute("CreatedBy",   Yii::app()->user->id);
						     $vmodel->save();
						     
						     //log
						     $utilLog = new Utils;
						     $utilLog->saveAuditLogs();
						     $this->redirect(array('view','id'=>$model->CustomerId));
						     return;
					     }
					}//cust-subsc-saved	
					else
					{
						$this->errorMessage = 'Add/Deduct Points failed.';
						$model->addError('error', $this->errorMessage);
					}
				}//no-errors 1 more
			}//no-errors			
			
		} // post

		

		//generic
		if(0)
		{
			echo "<pre>$id=OKS# " .@var_export($model,1)."</pre>";
			exit;
		}

		//chk if exists
		$ClientId = 0;
		$cid      = addslashes($id);
		$cusm     = Customers::model()->findAll(array(
					'select'    => '*', 
					'condition' => " CustomerId = '$cid'  "
					));
		if($cusm and @count($cusm)>0)
		{
			$ClientId = $cusm[0]->ClientId;
		}

		$this->render('addsub',array(
			'model'         => $model,
			'CustomerId'    => $id,
			'brand_list'    => $this->getBrandList($ClientId),
			'campaign_list' => $this->getCampaignList($ClientId),
			'point_list'    => $this->getActionTypeList($ClientId),
			'client_list'   => $this->getClientsList(),
			'error_msgs'    => $this->errorMessage,
		));
	}
	
	
	protected function getCustomerList()
	{

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

		$list  = array();

		if(1){

			$rawSql = "
				SELECT  t.*
				FROM customers t
				WHERE 1=1 
					$xtra1 
					$xtra2
					$xtra3
					$xtra4
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField' => 'CustomerId',
				    'totalItemCount' => $rawCount,
				    )
				);

		}

		$res = array();
		foreach($dataProvider->getData() as $row)
		{
			$res[$row["CustomerId"]] = sprintf("%s %s - %s",
							$row["FirstName"],
							$row["LastName"],
							$row["Email"]);
		}
		//give
		return $res;
	}
	
	protected function getClientsList()
	{

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

		

		if(1){

			$rawSql = "
				SELECT  t.*
				FROM clients t
				WHERE 1=1 
					$xtra1 
					$xtra2
					$xtra3
					$xtra4
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField'       => 'ClientId',
				    'totalItemCount' => $rawCount,
				    )
				);

		}
		
		$res = array();
		foreach($dataProvider->getData() as $row)
		{
			$res[$row["ClientId"]] = trim($row["CompanyName"]);
						
		}
		//give
		return $res;
	}

	protected function getBrandList($cid=0)
	{
	
		$xmore = '';
		$ymore = '';

		$cid   = addslashes($cid); 
		$xmore = " AND t.ClientId = '$cid' ";

		//get it
		$model = Brands::model()->findAll(array(
				'select'=>'*',  'condition' => " status = 'ACTIVE' $xmore $ymore "));

		$res   = array();
		foreach($model as $row) 
		{
			$res[$row->BrandId] = $row->BrandName;
		}
		//give it back
		return $res;
	}

	protected function getCampaignList($cid=0)
	{
	
		$xmore = '';
		$ymore = '';

		$cid   = addslashes($cid); 
		$xmore = " AND t.ClientId = '$cid' ";


		//get it
		$model = Campaigns::model()->findAll(array(
				'select' => '*',  'condition' => " status = 'ACTIVE' $xmore $ymore "));

		$res   = array();
		foreach($model as $row) 
		{
			$res[$row->CampaignId] = $row->CampaignName;
		}
		
		//give it back
		return $res;
	}
	
	protected function getActionTypeList($cid=0)
	{
	
		$xmore = '';
		$ymore = '';

		$cid   = addslashes($cid); 
		$xmore = " AND t.ClientId = '$cid' ";


		//get it
		$model = ActionType::model()->findAll(array(
				'select' => '*',  'condition' => " status = 'ACTIVE' $xmore $ymore "));

		$res   = array();
		foreach($model as $row) 
		{
			$kk = sprintf("%s-%s",$row->ActiontypeId,$row->PointsId);
			$res[$kk] = $row->Name;
		}
		
		//give it back
		return $res;
	}	
	
	
}
