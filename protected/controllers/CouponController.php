<?php

class CouponController extends Controller
{
	public $extraJS;
	public $mainDivClass;
	public $modals;	
	public $mappings;
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','index','view','pending','approve'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','pending','approve'),
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
		//error_reporting(E_ALL|E_STRICT);
		$model=new Coupon;
		

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		if(isset($_POST['Coupon']))
		{
            $model->attributes=$_POST['Coupon'];
            $model->CouponMode=$_POST['Coupon']['CouponMode'];

            $model->TypeId='0';

            $params=$_POST['Coupon'];

            // // var_dump($params); exit;

            if(! isset($params['ClientId'])) $params['ClientId'] = array();
            if(! isset($params['BrandId'])) $params['BrandId'] = array();
            if(! isset($params['CampaignId'])) $params['CampaignId'] = array();
            if(! isset($params['ChannelId'])) $params['ChannelId'] = array();
			/*
            if(Yii::app()->user->AccessType === "SUPERADMIN" && $model->scenario === 'insert') {
                $arr_clients = $params['ClientId'];
            } else { // not super admin
                $arr_clients = array( Yii::app()->user->ClientId );
            }
			*/
			if(Yii::app()->utils->getUserInfo('AccessType') === 'ADMIN')
			{
				$arr_clients = array( Yii::app()->user->ClientId );
			}
			else
			{
				$arr_clients = $params['ClientId'];
			}
            $arr_brands  = $params['BrandId'];
            $arr_campaigns = $params['CampaignId'];
            $arr_channels  = $params['ChannelId'];

            // // $params['RewardId'] = (int) $params['RewardId'];

            // /**
            //  * Preliminary validation
            //  */
            // // if(empty($params['RewardId'])) $model->addError('RewardId', 'Reward cannot be blank.');
            if(empty($arr_clients)) $model->addError('ClientId', 'Client cannot be blank.');
            if(empty($arr_brands))  $model->addError('BrandId', 'Brand cannot be blank.');
            if(empty($arr_campaigns)) $model->addError('CampaignId', 'Campaign cannot be blank.');
            if(empty($arr_channels))  $model->addError('ChannelId', 'Channel cannot be blank.');
            // End validation

            if(! $model->hasErrors())
            {

                $model->attributes=$params;
    			
    			// search by channel_id
    			//$chann = implode(',', $arr_channels);
    			//$test = Channels::model()->findAllByPk('1');
    			//$test = Channels::model()->findAll("Channelid IN ({$chann})");
    			//echo '<pre>';
    			// print_r($test[0]->attributes);
    			//print_r($test[0]->ChannelId);
    			//exit();
    			

                // We get the image information using CUploadedFile class.
                // The model object and attribute name is passed when the getInstance method is called.
                $couponUploadFile = CUploadedFile::getInstance($model,'File');
                
                if($params['CouponMode'] === 'user') {
    				$model->TypeId = 'USER-GENERATED';
                    if ($couponUploadFile !== null && $couponUploadFile->getSize()) {
    					$couponFilename = md5(uniqid()) . '_' . $couponUploadFile->name;
    					$model->File = '/var/www/html/multichannel/protected/uploads/coupons/' . $couponFilename;
                    }
                    else {
                        $model->addError('File', 'File cannot be empty.');
                    }
                } else {
    				$model->TypeId = 'SYTEM-GENERATED';
                    $model->File = null;
                }
                
                $UploadFile = CUploadedFile::getInstance($model,'Image');

                if ($UploadFile !== null) {
                    $imageFilename = md5(uniqid()) . '_' . $UploadFile->name;
                    $model->Image = Yii::app()->params['baseUploadUrl'] . 'coupon/' . $imageFilename;
                    
                } else {
                    $model->addError('Image', 'File cannot be empty.');
                }
        
    			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
    			$model->setAttribute("CreatedBy", Yii::app()->user->id);
    			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
    			$model->setAttribute("UpdatedBy", Yii::app()->user->id);

    			// var_dump($model->TypeId); exit;
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $saved = $model->save();
        			if( $saved )
        			{
        				try {
        	                if($couponUploadFile !== null) {
        	                    $couponUploadFile->saveAs(Yii::app()->params['uploadCouponDir'] . $couponFilename);
        	                }
        	            } catch (Exception $ex) {
        	            	$model->addError('File', 'Failed to upload file.');
        	            	Yii::app()->user->setFlash('error', 'Error: ' . $ex->getMessage());
                            $transaction->rollback();
        	            }
        				
        				try {
        	                if($UploadFile !== null) {
        	                    $UploadFile->saveAs(Yii::app()->params['uploadImageDir'] . 'coupon/'  . $imageFilename);
        	                }
        	            } catch (Exception $ex) {
        	            	$model->addError('Image', 'Failed to upload image.');
        	            	Yii::app()->user->setFlash('error', 'Error: ' . $ex->getMessage());
                            $transaction->rollback();
        	            }
        				// $this->redirect(array('view','id'=>$model->CouponId));

                        if(! $model->hasErrors()) {
                            Yii::app()->user->setFlash('success','Coupon saved.');
                            $transaction->commit();
                        }
        			} else {
                        Yii::app()->user->setFlash('error','Unable to save coupon.');
                        $transaction->rollback();
                    }
                } catch( CDbException $ex ) {
                    Yii::app()->user->setFlash('error','Unable to save coupon.');
                    $transaction->rollback();
                } catch( Exception $ex ) {
                    Yii::app()->user->setFlash('error','An unexpected error was encountered.');
                    $transaction->rollback();
                }

                // When coupon is saved, build insert statement to coupon mapping.
                if( $saved ) {
                    $arr_coupon_map = array();
    				
    				// search by channel_id
    				$chann = implode(',', $arr_channels);
	
    				//$test = Channels::model()->findAllByPk('1');
    				$coupmap = Channels::model()->findAll("Channelid IN ({$chann})");
    				// echo '<pre>';
    				// print_r($coupmap[0]);
    				// exit();
    				
    				for ($i=0; $i<count($coupmap); $i++)
    				{
    					$brandchann = Brands::model()->findAllByPk($coupmap[$i]->BrandId);
    					$clientchann = Clients::model()->findAllByPk($brandchann[0]->ClientId);
    					$arr_coupon_map[] = array(
    						'CouponId'=>$model->CouponId,
    						'ClientId'=>$clientchann[0]->ClientId,
    						'BrandId'=>$coupmap[$i]->BrandId,
    						'CampaignId'=>$coupmap[$i]->CampaignId,
    						'ChannelId'=>$coupmap[$i]->ChannelId,
    					);
    				}
    /*
                    foreach ($arr_clients as $idx => $l_client) {
                        foreach ($arr_brands as $idx => $l_brand) {
                            foreach ($arr_campaigns as $idx => $l_campaign) {
                                foreach ($arr_channels as $idx => $l_channel) {
                                    $arr_coupon_map[] = array(
                                        'CouponId'=>$model->CouponId,
                                        'ClientId'=>$l_client,
                                        'BrandId'=>$l_brand,
                                        'CampaignId'=>$l_campaign,
                                        'ChannelId'=>$l_channel,
                                    );
                                } // loop ng channels
                            } // loop ng campaign
                        } // loop ng brands
                    } // loop ng clients
    */
                    $transaction2 = Yii::app()->db->beginTransaction();
                    try {
                        $builder = Yii::app()->db->schema->commandBuilder; 
                        $command=$builder->createMultipleInsertCommand('coupon_mapping', $arr_coupon_map);
                        $command->execute();

                        $transaction2->commit();
                    } catch (CDbException $ex) {
                        $transaction2->rollback();
                    }
                }
            }
		}
		
		
		// $clientsID = Users::model()->findByPk(Yii::app()->user->id)->ClientId;
		// $_clients = Clients::model()->findAll(array(
		// 	'select'=>'ClientId, CompanyName', 'condition'=>'ClientId='.$clientsID.''));
        $criteria = new CDbCriteria;
        $criteria->select = 'ClientId, CompanyName';

        $_clients = Clients::model()->active()->findAll($criteria);
    	$clients = CHtml::listData($_clients, 'ClientId', 'CompanyName');

		$criteria = new CDbCriteria;
		$criteria->select = 'BrandId, BrandName';
		$criteria->condition = 'status=\'ACTIVE\'';
		
/*
		if($model->ClientId) {

			$criteria->addCondition('ClientId = :inClientId');
			$criteria->params[':inClientId'] = $model->ClientId;
		} else if($clients) {

			$criteria->addCondition('ClientId = :inClientId');
			$criteria->params[':inClientId'] = $_clients[0]->ClientId;
		}
*/

		$_brands = Brands::model()->thisClient()->active()->findAll($criteria);
		$brands = CHtml::listData($_brands, 'BrandId', 'BrandName');

		$_campaigns = Campaigns::model()->findAll(array(
			'select'=>'CampaignId, BrandId, CampaignName', 'condition'=>'status=\'ACTIVE\''));

		$campaigns = CHtml::listData($_campaigns, 'CampaignId', 'CampaignName');

		$_channels = Channels::model()->findAll(array(
			'select'=>'ChannelId, BrandId, CampaignId, ChannelName', 'condition'=>'status=\'ACTIVE\''));

		$channels = CHtml::listData($_channels, 'ChannelId', 'ChannelName');

		$this->render('create',array(
			'model'=>$model,
			'client_list'=>$clients,
			'brand_id'=>$brands,
			'channel_id'=>$channels,
			'campaign_id'=>$campaigns
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

        $model->CouponMode = ($model->File) ? 'user' : 'system';

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		if(isset($_POST['Coupon']))
		{
            $couponUploadFile = CUploadedFile::getInstance($model,'File');
			// $model->attributes=$_POST['Coupon'];
            if($model->CouponMode==='user') {
				if ($couponUploadFile !== null && $couponUploadFile->getSize()) {
                // if ($couponUploadFile !== null) {
                    $couponFilename = md5(uniqid()) . '_' . $couponUploadFile->name;
                    $model->File = '/var/www/html/multichannel/protected/uploads/coupons/' . $couponFilename;
                }
                else {
                    $model->addError('File', 'File cannot be empty.');
                }
            }
            else {
                if(intval($_POST['Coupon']['Quantity']) <= intval($model->Quantity) ) {
                    $model->addError('error', 'Quantity must be more than the current value.');
                } else {
                    $model->Quantity = $_POST['Coupon']['Quantity'];
                }
            }

            $model->ExpiryDate = $_POST['Coupon']['ExpiryDate'];

            if($model->Status==='ACTIVE') $model->edit_flag = "1";

			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);

            if(! $model->hasErrors())
            {
                $transaction = Yii::app()->db->beginTransaction();
                try {
        			if($model->save()) {
                        if($model->CouponMode==='user') {
                            try {
                                if($couponUploadFile !== null) {
                                    $couponUploadFile->saveAs(Yii::app()->params['uploadCouponDir'] . $couponFilename);
                                }
                            } catch (Exception $ex) {
                                $model->addError('File', 'Failed to upload file.');
                                Yii::app()->user->setFlash('error', 'Error: ' . $ex->getMessage());
                                $transaction->rollback();
                            }
                        }

                        if(! $model->hasErrors()) {
                            Yii::app()->user->setFlash('success','Coupon saved.');
                            $transaction->commit();
                        } else {
                            Yii::app()->user->setFlash('error','Coupon not saved.');
                            $transaction->rollback();
                        }
                    }
                } catch (CDbException $ex) {
                    Yii::app()->user->setFlash('error','Unable to save coupon.');
                    $transaction->rollback();
                } catch (Exception $ex) {
                    Yii::app()->user->setFlash('error','Unexpected error encountered.');
                    $transaction->rollback();
                }
            }
		}

		$this->render('update',array(
			'model'=>$model,
			// 'client_list'=>$clients,
			// 'brand_id'=>$brands,
			// 'channel_id'=>$channels,
			// 'campaign_id'=>$campaigns
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
		
			//relations
			$criteria->with = array('couponMap',
			                        'couponClients');
			$criteria->together = true; // ADDED THIS
			$criteria->condition = "couponMap.CouponId = t.CouponId AND 
                        			couponMap.ClientId = couponClients.ClientId AND 
                        			couponClients.ClientId= '".addslashes(Yii::app()->user->ClientId)."' ";
		}

		//create data
		$dataProvider = new CActiveDataProvider('Coupon', array(
			'criteria'=>$criteria ,
		));
		
		if(0){
			echo "####<hr>".@var_dump($dataProvider->getData(),true);
			exit;
		}
		
		
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Coupon('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Coupon']))
			$model->attributes=$_GET['Coupon'];

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
		
		
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN')   
		{

			//relations
			$criteria->with = array('couponMap',
						'couponClients',
						'couponMap.couponClients',
						'couponMap.couponBrands',
						'couponMap.couponChannels',
						'couponMap.couponCampaigns');
			$criteria->together  = true; // ADDED THIS
			$criteria->condition = " t.Status IN ('ACTIVE','PENDING') AND t.edit_flag <= '0' AND 
						 couponMap.CouponId = t.CouponId AND 
						 couponMap.ClientId = couponClients.ClientId AND 
						 couponClients.ClientId= '".addslashes(Yii::app()->user->ClientId)."' ";
		}
		else
		{
			//relations
			$criteria->with = array('couponMap',
						'couponClients',
						'couponMap.couponClients',
						'couponMap.couponBrands',
						'couponMap.couponChannels',
						'couponMap.couponCampaigns');
			$criteria->together  = true; // ADDED THIS
			$criteria->condition = " t.Status IN ('ACTIVE','PENDING') AND t.edit_flag <= '0' AND 
						 couponMap.CouponId = t.CouponId AND 
						 couponMap.ClientId = couponClients.ClientId "; 
		}
		//try 
		if(0){
		$criteria->with=array(
		'couponMap.couponClients',
		'couponMap.couponBrands',
		'couponMap.couponChannels',
		'couponMap.couponCampaigns',
		'couponMap',
		);
		}
    		//provider
    		$dataProvider = new CActiveDataProvider('Coupon', array(
				'criteria'=>$criteria ,
			));
    		
    		if(0){
    		
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
    		
    		
		
		//send it
		$dataProvider = new CActiveDataProvider('Coupon', array(
			'criteria'=>$criteria ,
		));
		
		$mapping =  array(
			'Brands'       => $brands,
			'Campaigns'    => $campaigns,
			'Clients'      => $clients,
			'Channels'     => $channels,
		);
		
		$this->render('pending',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
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

		//all-pending
		$criteria->addCondition("t.Status IN ('ACTIVE','PENDING') ");
		$criteria->addCondition("t.edit_flag = '1' ");

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
	
		$mapping =  array(
			'Brands'       => $brands,
			'Campaigns'    => $campaigns,
			'Clients'      => $clients,
			'Channels'     => $channels,
		);	
		
		//statys msg
		$this->statusMsg = '';
		$apiUtils  = new Utils;
		$uid       = trim(Yii::app()->request->getParam('uid'));
		
		//chk
		if(Yii::app()->user->AccessType !== "SUPERADMIN")
		{
		    $this->statusMsg = Yii::app()->params['notAllowedStatus'];
		}
		else
		{
		    
		    $api   = array(
		    		'data' => array('coupon_id'     => $uid, 
		    			        'update_coupon' => true),
		    		'url'  => Yii::app()->params['api-url']['update_coupon'],
		    		);
		    $ret   = $apiUtils->send2Api($api);
		    
		    $this->statusMsg = ( ( $ret["result_code"] == 200) ?
		                       ( 'Successfully updated the  coupon.' ) :
		                       ( sprintf("Error occurred while updating the  coupon.<br/><br/>[%s]",trim($ret["error_txt"]))) );
		}
		
		//provider
    		$dataProvider = new CActiveDataProvider('Coupon', array(
				'criteria'  =>$criteria ,
			));		
			
		$this->render('pending',array(
			'dataProvider' => $dataProvider,
			'mapping'      => $mapping,
			));			
	}




	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Coupon the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Coupon::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Coupon $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='coupon-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
