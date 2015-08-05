<?php

class ReportsController extends Controller
{
	public $extraJS;
	public $mainDivClass;
	public $modals;
	
	public function actionCreate()
	{
		$model = new Reports;
		$_brands = Brands::model()->thisClient()->findAll(array(
			'select'=>'BrandId, BrandName', 'condition'=>'status=\'ACTIVE\''));
		$brands = CHtml::listData($_brands, 'BrandId', 'BrandName');
		
		$_campaigns = Campaigns::model()->findAll(array(
			'select'=>'CampaignId, BrandId, CampaignName', 'condition'=>'status=\'ACTIVE\''));
		$campaigns = CHtml::listData($_campaigns, 'CampaignId', 'CampaignName');
		
		$_channels = Channels::model()->with('channelCampaigns')->findAll(array('condition'=>'t.status=\'ACTIVE\''));
		
		$channels = array(); //CHtml::listData($_channels, 'ChannelId', 'ChannelName');
		foreach($_channels as $row) {
			$channels[$row->ChannelId] = "{$row->ChannelName} ({$row->channelCampaigns->CampaignName})";
		}
		$customers = array(); //CHtml::listData($_channels, 'ChannelId', 'ChannelName');
		$_customers = Customers::model()->findAll();
		foreach($_customers as $row) {
			$customers[$row->CustomerId] = "{$row->Email}";
		}
		/*
		$form_view = $this->renderPartial('_form', array(
			'model'=>$model,
			'brands_list'=>$brands,
			'channels_list'=>$channels,
			'campaigns_list'=>$campaigns,
			'customers_list'=>$customers,
		), true);
		*/

		if(isset($_POST['Reports'])) {
			Yii::app()->user->setFlash('notice', 'Something went wrong and the developers are working on it.');

		}


		$_form = $this->renderPartial('_form2', array(
		    'model'=>$model,
		    'brands'=>$brands,
		    'campaigns'=>$campaigns,
		    'channels'=>$channels,
		    'customers'=>$customers,
		), true);
		// echo '<pre>';
		// print_r($brands);
		// print_r($campaigns);
		// print_r($channels);
		// print_r($customers);
		// exit();

		$this->render('create2', array(
		    'model'=>$model,
		    '_form'=>$_form,
		));
		
	}

	public function actionDelete()
	{
		$this->render('delete');
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionUpdate()
	{
		$this->render('update');
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}