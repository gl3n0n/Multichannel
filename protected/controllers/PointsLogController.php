<?php

class PointsLogController extends Controller
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
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($id)
	{
		$log_id = Yii::app()->getRequest()->getParam('id');
		
		// get details
		$model=PointsLog::model()->findByPk($log_id);
		
		// get point value
		$modelpt=Points::model()->findByPk($model->PointsId);
		
		$params = array('subscription_id' => $model->SubscriptionId,
						'channel_id' => $model->ChannelId, 'campaign_id' => $model->CampaignId, 'brand_id' => $model->BrandId,
						'points_id' => $model->PointsId, 'points' => $modelpt->Value, 'client_id' => $model->ClientId, 'customer_id' => $model->CustomerId, 'action' => 'ADD');
						
		$url = Yii::app()->params['updatePoints'];
		$output = Yii::app()->curl->post($url, $params);
		
		$this->redirect(array('/pointsLog/?points_id=' . $model->PointsId));
		/*
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PointsLog']))
		{
			$model->attributes=$_POST['PointsLog'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->PointLogId));
		}

		$this->render('create',array(
			'model'=>$model,
		));
		*/
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

		if(isset($_POST['PointsLog']))
		{
			$model->attributes=$_POST['PointsLog'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->PointLogId));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	public function actionRemove($id)
	{
		$url = Yii::app()->params['updatePoints'];
		$model=PointsLog::model()->findByPk($id);
		$params = array('point_log_id' => $model->PointLogId, 'subscription_id' => $model->SubscriptionId,
						'channel_id' => $model->ChannelId, 'campaign_id' => $model->CampaignId, 'brand_id' => $model->BrandId,
						'points_id' => $model->PointsId, 'client_id' => $model->ClientId, 'action' => 'SUBTRACT');
		$output = Yii::app()->curl->post($url, $params);
		
		// $this->redirect(Yii::app()->createUrl('/pointsLog/?points_id=' . $model->PointsId));
		$this->redirect(array('/pointsLog/?points_id=' . $model->PointsId));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		//$this->loadModel($id)->delete();
		$url = Yii::app()->params['updatePoints'];
		/*echo $url;
		exit();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		*/
	}
	
	public function actionPointid($points_id=0)
	{
		$pointsId = Yii::app()->getRequest()->getParam('points_id');
		$criteria = new CDbCriteria;
		$criteria->addCondition('SubscriptionId = :pointsId');
		$criteria->addCondition('ClientId = :clientId');
		$criteria->params = array(':pointsId' => $pointsId, ':clientId' => Yii::app()->user->ClientId);
		$dataProvider = new CActiveDataProvider('PointsLog', array(
				'criteria'=> $criteria,
			));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new PointsLog('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PointsLog']))
			$model->attributes=$_GET['PointsLog'];

		$criteria = new CDbCriteria;
		$criteria->scopes = array('thisClient');

		$dataProvider = new CActiveDataProvider('PointsLog', array(
				'criteria'=>$criteria,
			));
			
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new PointsLog('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PointsLog']))
			$model->attributes=$_GET['PointsLog'];

		$this->render('admin',array(
			'model'=>$model,
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
}
