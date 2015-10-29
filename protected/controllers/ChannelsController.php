<?php

class ChannelsController extends Controller
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
				'actions'=>array('index','view','create','update','getCampaigns','getChannels','getBrands'),
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

    public function actionGetBrands($ClientId=null)
    {
        if( ! (intval($ClientId)) ) return Yii::app()->utils->sendJSONResponse(array());

        $model = Brands::model()->findAllByAttributes(array('ClientId'=>$ClientId), array('select'=>'BrandId, BrandName'));
        $list  = CHtml::listData($model, 'BrandId', 'BrandName');
        // $list_options = CHtml::listOptions('', $list, array());
        // foreach($model as $row) { $list[$row['CampaignId']] = $row['CampaignName']; }
        

        Yii::app()->utils->sendJSONResponse($list);

    }

    public function actionGetCampaigns($BrandId=null)
    {
        // $BrandId = Yii::app()->request->getParam('BrandId', 0);
        if( ! (intval($BrandId)) ) return Yii::app()->utils->sendJSONResponse(array());

        $model = Campaigns::model()->findAllByAttributes(array('BrandId'=>$BrandId), array('select'=>'CampaignId, CampaignName'));
        $list  = array();

        foreach($model as $row) { $list[$row['CampaignId']] = $row['CampaignName']; }
		

        Yii::app()->utils->sendJSONResponse($list);
    }

    public function actionGetChannels()
    {
        $BrandId = (int) Yii::app()->request->getParam("BrandId", "0");
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

		// $channels = array(); //CHtml::listData($_channels, 'ChannelId', 'ChannelName');
		foreach($model as $row) {
			$list[$row->CampaignId.'-'.$row->ChannelId] = "{$row->ChannelName} ({$row->channelCampaigns->CampaignName})";
		}
		
        // foreach($model as $row) { $list[$row['ChannelId']] = $row['ChannelName']; }
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
        error_reporting(E_ALL|E_STRICT);
        $errors='';
        $model=new Channels;
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
		/*
		$_clients = Clients::model()->thisClient()->findAll(array(
            'select'=>'ClientId, CompanyName', 'condition'=>'status=\'ACTIVE\''));
        $brands = array();
        foreach($_brands as $row) {
            $brands[$row->ClientId] = $row->CompanyName;

        }
        */
        $_brands = Brands::model()->thisClient()->findAll(array(
            'select'=>'BrandId, BrandName', 'condition'=>'status=\'ACTIVE\''));
        $brands = array();
        foreach($_brands as $row) {
            $brands[$row->BrandId] = $row->BrandName;

        }
        
        $_campaigns = Campaigns::model()->findAll(array(
            'select'=>'CampaignId, BrandId, CampaignName', 'condition'=>'status=\'ACTIVE\''));
        $campaigns = array();
        foreach($_campaigns as $row) {
            $campaigns[$row->CampaignId] = $row->CampaignName;

        }

        if(isset($_POST['Channels']))
        {
            $campaigns = $_POST['Channels']['CampaignId'];
            unset($_POST['CampaignId']);

            $transaction = Yii::app()->db->beginTransaction();

            try
            {
                foreach ($campaigns as $idx => $row)
                {
                    if($idx > 0) $model=new Channels;
                    $model->attributes=$_POST['Channels'];
                    $model->CampaignId=intval($row);
					// $model->setAttribute("ClientId", Yii::app()->user->ClientId); // this will change if SUPERADMIN
					if(Yii::app()->utils->getUserInfo('AccessType') === 'ADMIN')
					{
						$model->setAttribute("ClientId", Yii::app()->user->ClientId);
					}
        			$model->setAttribute("DateCreated", new CDbExpression('NOW()'));
        			$model->setAttribute("CreatedBy", Yii::app()->user->id);
        			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
        			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
                    if($model->save())
                    {
			$utilLog = new Utils;
			$utilLog->saveAuditLogs();
                    }
                }
                $save = $transaction->commit();
				$this->redirect('index');
            }
            catch(CDbException $ex)
            {
                $errors='Failed to save data. Please try again.';
                $transaction->rollback();
            }
		}

		$this->render('create',array(
			'model'=>$model,
			'brand_id'=>$brands,
			'campaign_id'=>$campaigns,
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
		
		$_brands = Brands::model()->thisClient()->findAll(array(
			'select'=>'BrandId, BrandName', 'condition'=>'status=\'ACTIVE\''));
		$brands = array();
		foreach($_brands as $row) {
			$brands[$row->BrandId] = $row->BrandName;

		}
		
		$_campaigns = Campaigns::model()->findAll(array(
			'select'=>'CampaignId, BrandId, CampaignName', 'condition'=>'status=\'ACTIVE\'')
        );
		$campaigns = array();
		foreach($_campaigns as $row) {
			$campaigns[$row->CampaignId] = $row->CampaignName;

		}

		if(isset($_POST['Channels']))
		{
			$old_attrs = @var_export($model->attributes,1);
			
			$model->attributes=$_POST['Channels'];
			
			
			$new_attrs = @var_export($model->attributes,1);
			$audit_logs= sprintf("OLD:\n\n%s\n\nNEW:\n\n%s",$old_attrs,$new_attrs);
			
			$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
			$model->setAttribute("UpdatedBy", Yii::app()->user->id);
			if($model->save())
			{
				$utilLog = new Utils;
				$utilLog->saveAuditLogs(null,$audit_logs);

				$this->redirect(array('view','id'=>$model->ChannelId));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'brand_id'=>$brands,
			'campaign_id'=>$campaigns,
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
		//name
		$byName   = trim(Yii::app()->request->getParam('byName'));
		if(strlen($byName))
		{
		    $t = addslashes($byName);
			$criteria->addCondition(" ( t.ChannelName     LIKE '%$t%' ) ");
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
			$criteria->addCondition(" ( t.DurationFrom >= '$t 00:00:00' ) ");
		}
		//date: 
		$byTranDateTo = trim(Yii::app()->request->getParam('byTranDateTo'));
		if(@preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$byTranDateTo))
		{
			$t = addslashes($byTranDateTo);
			$criteria->addCondition(" ( t.DurationTo <= '$t 23:59:59' ) ");
		}		

		
		


		if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN') {
			$dataProvider = new CActiveDataProvider('Channels', array(
			'criteria'=>$criteria ,
			));
		} else {
			if(0)
			{
				$dataProvider = new CActiveDataProvider('Channels', array(
					'criteria'=>array(
					    'scopes'=>array('thisClient'),
					),
				));
			}
			$criteria->compare('ClientId', Yii::app()->user->ClientId, true); 
			$dataProvider = new CActiveDataProvider('Channels', array(
					'criteria'=>$criteria ,
			));
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
		$model=new Channels('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Channels']))
			$model->attributes=$_GET['Channels'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Channels the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Channels::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Channels $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='channels-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
