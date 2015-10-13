<?php

class RaffleController extends Controller
{
	public $extraJS;
	public $mainDivClass;
	public $modals;
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
				'actions'=>array('create','update',
				'pending','approve','genraffle','drawwinner'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('create','update',
				'pending','approve','genraffle','drawwinner'),
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
		$model=new Raffle;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		
		if(Yii::app()->user->AccessType === "SUPERADMIN") {
			$_coupon = CouponSystem::model()->findAll();
		} else {
			$_coupon = CouponSystem::model()->thisClient()->findAll();
		}
		$coupons = array();
		foreach($_coupon as $row) {
			$coupons[$row->CouponId] = $row->CouponName;

		}

		if(isset($_POST['Raffle']))
		{

			$cid   = 0;
			$ctype = 0;
			$more  = '';
			if(1){
				foreach($_coupon as $row) {
					if($row->CouponId == $_POST['Raffle']['CouponId'])
					{
						$cid = $row->ClientId;
						if(@preg_match("/^(REGULAR)$/i",$row->CouponType))
							$ctype++;
						$more = sprintf("%s - %s",$row->CouponType,$row->CouponName);
						break;
					}
				}
			}
			$model->attributes=$_POST['Raffle'];
			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
			$model->setAttribute("CreatedBy", Yii::app()->user->id);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if(Yii::app()->user->AccessType !== "SUPERADMIN" && $model->scenario === 'insert') {
				$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			}
			if(Yii::app()->user->AccessType == "SUPERADMIN" && $model->scenario === 'insert') {
				$model->setAttribute("ClientId", $cid);
			}

			if(! $ctype)
			{
				$model->addError('CouponId', 'Allowed Coupon Type is REGULAR only.');
			}
			else
			{
				if($model->save()){
					$utilLog = new Utils;
					$utilLog->saveAuditLogs();
					$this->redirect(array('view','id'=>$model->RaffleId));
				}
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'coupon_id'=>$coupons
			
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
		

		$_coupon = CouponSystem::model()->findAll();
		$coupons = array();
		foreach($_coupon as $row) {
			$coupons[$row->CouponId] = $row->CouponName;

		}

		if(isset($_POST['Raffle']))
		{
			$model->attributes=$_POST['Raffle'];
			$model->setAttribute("ClientId", Yii::app()->user->ClientId);
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save()){
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
				$this->redirect(array('view','id'=>$model->RaffleId));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'coupon_id'=>$coupons
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
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		if($search) $criteria->compare('Source', $search, true);


		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') 
		{
			 $criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
		}


		$dataProvider = new CActiveDataProvider('Raffle', array(
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
		$model=new Raffle('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Raffle']))
			$model->attributes=$_GET['Raffle'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}



	

	/**
	 * Manages all models.
	 */
	public function actionPending()
	{
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		if($search) $criteria->compare('Source', $search, true);

		//all-pending
		$criteria->addCondition("t.Status IN ('ACTIVE','PENDING') ");
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') 
		{
			 $criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
		}

    		//provider
    		$dataProvider = new CActiveDataProvider('Raffle', array(
				'criteria'=>$criteria ,
			));
    		
		
		$this->render('pending',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $this->getMoreLists(),
		));
	}
	
		

	/**
	* approve via API.
	*/
	public function actionApprove()
	{
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		if($search) $criteria->compare('Source', $search, true);


		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') 
		{
			 $criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
		}

		//statys msg
		$this->statusMsg = '';
		$apiUtils  = new Utils;
		$uid       = trim(Yii::app()->request->getParam('uid'));
		$model     = Raffle::model()->findByPk($uid);
		
		
		//chk
		if(Yii::app()->user->AccessType !== "SUPERADMIN")
		{
		    $this->statusMsg = Yii::app()->params['notAllowedStatus'];
		}
		else
		{
		    
		    $api   = array(
		    		'data' => array('raffle_id'     => $uid, 
		    				'status'        => 'ACTIVE',
		    				'updated_by'    => $model->UpdatedBy,
		    				'source'        => $model->Source,
		    				'no_of_winners' => $model->NoOfWinners,
		    				'draw_date'     => $model->DrawDate,
		    			        'update_raffle' => true),
		    		'url'  => Yii::app()->params['api-url']['update_raffle'],
		    		);
		    $ret   = $apiUtils->send2Api($api);
		    
		    $this->statusMsg = ( ( $ret["result_code"] == 200) ?
		                       ( 'Successfully generated the raffle.') :
		                       ( sprintf("Error occurred while generating the  raffle.<br/><br/>[%s]",trim($ret["error_txt"]))) );
		    if($ret["result_code"] == 200)
		    {
			$utilLog = new Utils;
			$utilLog->saveAuditLogs();
		    }
		}
		
		$criteria->addCondition("t.Status IN ('ACTIVE','PENDING') ");
		
		//provider
    		$dataProvider = new CActiveDataProvider('Raffle', array(
				'criteria'  =>$criteria ,
			));		
			
		$this->render('pending',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $this->getMoreLists(),
			));			
	}



	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Raffle the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Raffle::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Raffle $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='raffle-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function getMoreLists()
	{

		$clid   = addslashes(Yii::app()->user->ClientId);
		$cand   = (Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  ? (" AND ClientId='$clid' ") : ('');
		
		//brands		
		$_brands = Brands::model()->findAll(array(
				'select'=>'BrandId, BrandName', 'condition'=>" status='ACTIVE' $cand "));
		$brands = CHtml::listData($_brands, 'BrandId', 'BrandName');

		//campaigns
		$_campaigns = Campaigns::model()->findAll(array(
			     'select'=>'CampaignId, CampaignName', 'condition'=>" status='ACTIVE' $cand "));
		$campaigns  = CHtml::listData($_campaigns, 'CampaignId', 'CampaignName');

		//clients		
		$_clients   = Clients::model()->findAll(array(
				'select'=>'ClientId, CompanyName', 'condition'=>" status='ACTIVE' $cand "));
		$clients    = CHtml::listData($_clients, 'ClientId',  'CompanyName');

		//channels
		$_channels   = Channels::model()->findAll(array(
				'select'=>'ChannelId, ChannelName', 'condition'=>" status='ACTIVE' $cand "));
		$channels    = CHtml::listData($_channels, 'ChannelId',  'ChannelName');


		//customers

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

	
	public function actionGenraffle()
	{
		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		//all-pending
		
		$raffleid   = trim(Yii::app()->request->getParam('raffleid'));
		$couponid   = trim(Yii::app()->request->getParam('couponid'));
		$numwinners = trim(Yii::app()->request->getParam('numwinners'));
		
		$clid   = addslashes(Yii::app()->user->ClientId);
		$xtra   = '';
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')  
		{
			$xtra   = " ";
		}
		
		if(1){
		$coups    = addslashes($couponid);
		$rawSql   = "
			SELECT distinct(Email),customers.CustomerId as CustomerId 
			FROM generated_coupons 
			JOIN customers on generated_coupons.CustomerId = customers.CustomerId 
			WHERE 1=1
				AND CouponId = '$coups'
		";
		$rawData  = Yii::app()->db->createCommand($rawSql); 
		$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
		$dataProvider    = new CSqlDataProvider($rawData, array(
					    'keyField'       => 'CustomerId',
					    'totalItemCount' => $rawCount,
					    )
			);
		
		}
    		
		$mapping = $this->getMoreLists();
		$mapping['mdata'] = array(
					'CouponId'    => $numwinners,
					'NoOfWinners' => $numwinners,
					'RaffleId'    => $raffleid,
				);
		
		$this->render('genparticipants',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
			
			
		));
	}

	public function actionDrawwinner()
	{
		$criteria = new CDbCriteria;
		
		//statys msg
		$this->statusMsg = '';
		$apiUtils  = new Utils;
		$uid       = trim(Yii::app()->request->getParam('uid'));
		$model     = Raffle::model()->findByPk($uid);
		
		$winners   = Yii::app()->request->getParam('winner');
		$CouponId  = Yii::app()->request->getParam('CouponId');
		$NoOfWinners  = Yii::app()->request->getParam('NoOfWinners');
		$RaffleId     = Yii::app()->request->getParam('RaffleId');
		
		//chk
		if(Yii::app()->user->AccessType !== "SUPERADMIN")
		{
		    $this->statusMsg = Yii::app()->params['notAllowedStatus'];
		}
		else
		{
		    
		    $api   = array(
		    		'data' => array('raffle_id'     => $RaffleId, 
		    				'status'        => 'ACTIVE',
		    				'participants'  => @join(",",$winners),
		    			        'draw_winner'   => true),
		    		'url'  => Yii::app()->params['api-url']['draw_winner'],
		    		);
		    $data   = $apiUtils->send2Api($api);
		    
			if ($data["result_code"] == 200)
			{
				$curr_win = '';
				$curr_bak = '';
				foreach ($data['winners'] as &$winner)
				{
					if(strlen(trim($winner["email"]))>0)
					$curr_win .= "<li><font color=\"green\">" . $winner["email"] . "</font></li>";
				}
				
				foreach ($data['backup_winners'] as &$winner)
				{
					if(strlen(trim($winner["email"]))>0)
					$curr_bak .= "<li><font color=\"green\">" . $winner["email"] . "</font></li>";
				}
				$this->statusMsg = "Notice: <font color='green'>Successfully generated winners.<br></font>
						   <br/><br/>
						   <ul>
						   $curr_win
						   </ul>
						   <br/><em>Backup Winners:</em><br/>
						   <ul>
						   $curr_bak
						   </ul>
						   ";
				$utilLog = new Utils;
				$utilLog->saveAuditLogs();
			}
			else
			{
				$this->statusMsg = "Notice: <font color='red'>Failed to Generate Winners.<br>  </font>";
			}
		}
		
		$criteria->addCondition("t.Status IN ('ACTIVE','PENDING') ");
		
		//provider
    		$dataProvider = new CActiveDataProvider('Raffle', array(
				'criteria'  =>$criteria ,
			));		
			
		$this->render('pending',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $this->getMoreLists(),
			));			
	}


	
}
