<?php

class PointsSystemMappingController extends Controller
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
				'actions'=>array('index','view','create','update','list','delete',
				'getCampaigns','getChannels','getBrands'),
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


	protected function getDropList()
	{
		// Uncomment the following line if AJAX validation is needed
		$xmore = '';
		if(Yii::app()->user->AccessType !== "SUPERADMIN") {
			$xmore = " AND ClientId = '".addslashes(Yii::app()->user->ClientId)."' ";
		}
		$_list = PointsSystem::model()->findAll(array(
			  'select'=>'PointsId, ClientId, Name', 'condition' => " status='ACTIVE' $xmore "));
		$data = array();
		foreach($_list as $row) {
			$vkey = sprintf("%s-%s",$row->PointsId ,$row->ClientId );
			$data["$vkey"] = $row->Name;

		}
		//give it back
		return $data;
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		
		$model = new PointsSystemMapping;

		if(isset($_POST['PointsSystemMapping']))
		{
			$model->attributes=$_POST['PointsSystemMapping'];
		
			//pre-checking
			if(empty($_POST['PointsSystemMapping']['PointsId'])) 
				$model->addError('PointsId', 'PointsId cannot be blank.');
			if(empty($_POST['PointsSystemMapping']['BrandId'])) 
				$model->addError('BrandId',  'Brand cannot be blank.');
			if(empty($_POST['PointsSystemMapping']['CampaignId'])) 
				$model->addError('CampaignId', 'Campaign cannot be blank.');
			if(empty($_POST['PointsSystemMapping']['ChannelId']))  
				$model->addError('ChannelId', 'Channel cannot be blank.');
			
			$saveOk  = 0;
			$saveDup = 0;
			if(!$model->hasErrors())
			{	
				//fmt
				list($PointsId, $ClientId ) = @explode('-',trim($_POST['PointsSystemMapping']['PointsId']));

				//arr
				$BrandIds     = $_POST['PointsSystemMapping']['BrandId'];

				//arr
				$CampaignIds = $_POST['PointsSystemMapping']['CampaignId'];

				//arr -> $row->CampaignId.'-'.$row->ChannelId
				$ChannelIds  = $_POST['PointsSystemMapping']['ChannelId'];

				if(0){
					echo "<hr>".@var_dump($_POST) ."<br>";
					exit;
				}

				//$model->addError('error', 'Must select a valid Points');
				$model->setAttribute("PointsId", $PointsId);

				//reset the campaignId
				$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
				$model->setAttribute("CreatedBy",   Yii::app()->user->id);
				$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
				$model->setAttribute("UpdatedBy",   Yii::app()->user->id);

				if(@is_array($ChannelIds))
				{
					foreach($ChannelIds as $KK => $VV)
					{
					    list($vClientId,$vBrandId,$vCampaignId, $vChannelId ) = @explode('-',trim($VV));
					    $model->setAttribute("CampaignId",  $vCampaignId);
					    $model->setAttribute("ChannelId",   $vChannelId);
					    $model->setAttribute("ClientId",    $vClientId);
					    $model->setAttribute("BrandId",     $vBrandId);
					    
					    
					    
					    //chk
					    $kcondchk   = array();
					    $kcondchk[] = " 1=1 ";
					    $kcondchk[] = " AND t.PointsId   = '".addslashes($PointsId).    "' ";
					    $kcondchk[] = " AND t.ClientId   = '".addslashes($vClientId).   "' ";
					    $kcondchk[] = " AND t.BrandId    = '".addslashes($vBrandId).    "' ";
					    $kcondchk[] = " AND t.CampaignId = '".addslashes($vCampaignId). "' ";
					    $kcondchk[] = " AND t.ChannelId  = '".addslashes($vChannelId).  "' ";
					    $ksql       = @implode('',$kcondchk);
					    $kmapping   = PointsSystemMapping::model()->findAll(array(
								'select'    => "PointMappingId, PointsId", 
								'condition' => "$ksql"));
					    if($kmapping == null)
					    { 
						    if($model->save())
						    {
							$utilLog = new Utils;
							$utilLog->saveAuditLogs();  
							$saveOk++;
						    }
					    }
					    else
					    {
					    	$saveDup++; //duplicate
					    }
					}
				}
			}
			
			//chk
			if($saveOk <= 0)
			{
			     if(!$model->hasErrors())
			     {
			       if($saveDup>0)
			       	$model->addError('warning', 'A duplicate record found!');
			       else
			        $model->addError('warning', 'An unexpected error occured.');
			     }
			}
			else
			{
			     Yii::app()->user->setFlash('success', 'Points System Mapping successfully created.');
			     $this->actionIndex();
			     return;
			}
			
		
		}
		
		$brand_list = (Yii::app()->user->AccessType !== "SUPERADMIN") ? $this->getBrands(Yii::app()->user->ClientId) : (array());
		
		$this->render('create',array(
			'model'              => $model,
			'points_system_list' => $this->getDropList(),
			'brand_list'    => $brand_list,
			'channel_list'  => array(),
			'campaign_list' => array(),

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

		if(isset($_POST['PointsSystemMapping']))
		{
			//pre-checking
			if(empty($_POST['PointsSystemMapping']['PointsId'])) 
				$model->addError('PointsId', 'PointsId cannot be blank.');
			if(empty($_POST['PointsSystemMapping']['BrandId'])) 
				$model->addError('BrandId',  'Brand cannot be blank.');
			if(empty($_POST['PointsSystemMapping']['CampaignId'])) 
				$model->addError('CampaignId', 'Campaign cannot be blank.');
			if(empty($_POST['PointsSystemMapping']['ChannelId']))  
				$model->addError('ChannelId', 'Channel cannot be blank.');
			
			$saveOk  = 0;
			$saveDup = 0;
			if(!$model->hasErrors())
			{	
				//fmt
				list($PointsId, $ClientId ) = @explode('-',trim($_POST['PointsSystemMapping']['PointsId']));
				$BrandId     = @trim($_POST['PointsSystemMapping']['BrandId']);

				//arr
				$CampaignIds = $_POST['PointsSystemMapping']['CampaignId'];

				//arr -> $row->CampaignId.'-'.$row->ChannelId
				$ChannelIds  = $_POST['PointsSystemMapping']['ChannelId'];

				if(0){
					echo "<hr>".@var_dump($_POST) ."<br>";
					exit;
				}

				//$model->addError('error', 'Must select a valid Points');
				$model->setAttribute("PointsId", $PointsId);
				$model->setAttribute("ClientId", $ClientId);
				$model->setAttribute("BrandId",  $BrandId);

				//reset the campaignId
				$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
				$model->setAttribute("CreatedBy",   Yii::app()->user->id);
				$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
				$model->setAttribute("UpdatedBy",   Yii::app()->user->id);

				if(@is_array($ChannelIds))
				{
					foreach($ChannelIds as $KK => $VV)
					{
					    list($vCampaignId, $vChannelId ) = @explode('-',trim($VV));
					    $model->setAttribute("CampaignId",  $vCampaignId);
					    $model->setAttribute("ChannelId",   $vChannelId);
					    
					    
					    
					    //chk
					    $kcondchk   = array();
					    $kcondchk[] = " 1=1 ";
					    $kcondchk[] = " AND t.PointsId   = '".addslashes($PointsId).   "' ";
					    $kcondchk[] = " AND t.ClientId   = '".addslashes($ClientId).   "' ";
					    $kcondchk[] = " AND t.BrandId    = '".addslashes($BrandId).    "' ";
					    $kcondchk[] = " AND t.CampaignId = '".addslashes($vCampaignId)."' ";
					    $kcondchk[] = " AND t.ChannelId  = '".addslashes($vChannelId). "' ";
					    $ksql       = @implode('',$kcondchk);
					    $kmapping   = PointsSystemMapping::model()->findAll(array(
								'select'    => "PointMappingId, PointsId", 
								'condition' => "$ksql"));
					    if($kmapping == null)
					    { 
						    if($model->save())
						    {
							$utilLog = new Utils;
							$utilLog->saveAuditLogs();  
							$saveOk++;
						    }
					    }
					    else
					    {
					    	$saveDup++; //duplicate
					    }
					}
				}
			}
			
			//chk
			if($saveOk <= 0)
			{
			     if(!$model->hasErrors())
			     {
			       if($saveDup>0)
			       {
					Yii::app()->user->setFlash('success', 'Points System Mapping successfully updated.');
					$this->actionIndex();
					return;
			       }
			       else
			          $model->addError('warning', 'An unexpected error occured.');
			     }
			}
			else
			{
			     Yii::app()->user->setFlash('success', 'Points System Mapping successfully updated.');
			     $this->actionIndex();
			     return;
			}
			
		} //POST		

		$this->render('update',array(
			'model'               => $model,
			'points_system_list'  => $this->getDropList(),
			'brand_list'          => $this->getBrands($model->ClientId),
			'campaign_list'       => $this->getCampaigns($model->BrandId),
			'channel_list'        => $this->getChannels($model->BrandId,$model->CampaignId),
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

		$search   = trim(Yii::app()->request->getParam('search'));
		$criteria = new CDbCriteria;
		$xtra     = '';
		//comp
		if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') {
			$xtra     = " AND t.ClientId = '".@addslashes(Yii::app()->user->ClientId)."' ";
					
		}
		if(strlen($search))
		{
			$criteria->with = array(
				'byPointsSystem'    => array('joinType'=>'LEFT JOIN'),
			);
		        $criteria->addCondition(" ( byPointsSystem.Name LIKE '%".addslashes($search)."%' ) $xtra ");
		}			

		$dataProvider = new CActiveDataProvider('PointsSystemMapping', array(
				'criteria'=>$criteria ,
			));
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
		$model=new PointsSystemMapping('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PointsSystemMapping']))
			$model->attributes=$_GET['PointsSystemMapping'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return PointsSystemMapping the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=PointsSystemMapping::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param PointsSystemMapping $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='pointssystemmapping-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
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


    public function actionGetBrands($PointsId=null)
    {
    	//ClientId=1-1
    	list($Point,$Client) = @explode('-',$PointsId);
        if( ! (intval($Client)) ) return Yii::app()->utils->sendJSONResponse(array("$PointsId" => "$Client"));

        $model = Brands::model()->findAllByAttributes(array('ClientId'=>$Client), array('select'=>'BrandId, BrandName'));
        $list  = CHtml::listData($model, 'BrandId', 'BrandName');

        Yii::app()->utils->sendJSONResponse($list);

    }

    public function actionGetCampaigns()
    {
	$BrandId    = Yii::app()->request->getParam("BrandId", array());
  	if( ! is_array($BrandId) && intval($BrandId)) {
		$BrandId = array( intval($BrandId) );
	}

	$criteria = new CDbCriteria;
	$criteria->addInCondition('BrandId',    $BrandId);
        $model = Campaigns::model()->findAll($criteria);
        $list  = array();

        foreach($model as $row) { $list[$row['CampaignId']] = $row['CampaignName']; }
		

        Yii::app()->utils->sendJSONResponse($list);
    }

    public function actionGetChannels()
    {
	    $BrandId    = Yii::app()->request->getParam("BrandId", array());
	    $CampaignId = Yii::app()->request->getParam("CampaignId", array());

	    if( ! is_array($BrandId) && intval($BrandId)) {
		    $BrandId = array( intval($BrandId) );
	    }

	    if( ! is_array($CampaignId) && intval($CampaignId)) {
		    $CampaignId = array( intval($CampaignId) );
	    }

	    $criteria = new CDbCriteria;
	    $criteria->addInCondition('t.BrandId',    $BrandId);
	    $criteria->addInCondition('t.CampaignId', $CampaignId);
	    $model = Channels::model()->with('channelCampaigns')->findAll($criteria);
	    $list  = array();

	    foreach($model as $row) {
		    $idxk          = sprintf("%s-%s-%s-%s",$row->ClientId,$row->BrandId,$row->CampaignId,$row->ChannelId);
		    $list["$idxk"] = "{$row->ChannelName} ({$row->channelCampaigns->CampaignName})";
	    }
	    Yii::app()->utils->sendJSONResponse($list);
    }

    public function actionGetChannelsORIG()
    {
        $BrandId    = (int) Yii::app()->request->getParam("BrandId", "0");
        $CampaignId = Yii::app()->request->getParam("CampaignId", array());

        if( ! $BrandId || empty($CampaignId) ) Yii::app()->utils->sendJSONResponse(array());

        $criteria = new CDbCriteria;
        $criteria->condition = "t.Status = 'ACTIVE'";
        $criteria->addCondition('t.BrandId = :filter_brand_id');
        $criteria->params[':filter_brand_id'] = $BrandId;

        if(is_array($CampaignId)) { $criteria->addInCondition('t.CampaignId', $CampaignId); }
        else { 
            $criteria->addCondition('t.CampaignId = :filter_campaign_id'); 
            $criteria->params[':filter_campaign_id'] = (int) $CampaignId;
        }

        $model = Channels::model()->with('channelCampaigns')->findAll($criteria);
        $list = array();

		foreach($model as $row) {
			$list[$row->CampaignId.'-'.$row->ChannelId] = "{$row->ChannelName} ({$row->channelCampaigns->CampaignName})";
		}
        Yii::app()->utils->sendJSONResponse($list);
    }

	
}
