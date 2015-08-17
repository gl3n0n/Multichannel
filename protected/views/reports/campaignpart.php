<?php
/* @var $this CustomerSubscriptionsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Reports',
);

//overwrite
if(1)
{
	$this->menu=array(
		array('label'=>'Breakdown of Points Gained',       'url'=>array('pointsgainbal')),
		array('label'=>'List of Campaigns Participated',   'url'=>array('campaignpart')),
		array('label'=>'List of Redemeed Rewards',         'url'=>array('redeemrewards')),
		array('label'=>'List of Redemeed Coupons',         'url'=>array('redeemcoupons')),
		array('label'=>'Customer Activity Report',         'url'=>array('customeractivity')),
	);
}
?>

<h1>List of Campaigns Participated</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reports/campaignpart"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Channel Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="ChannelName" title="Search Channel Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
		array(
			'name' => 'ClientId',
			'value' => '$data["CompanyName"]',
			),
		array(
			'name' => 'BrandId',
			'value' => '$data["BrandName"]',
			),
		array(
			'name' => 'CampaignId',
			'value' => '$data["CampaignName"]',
			),
		/**
		array(
			'name' => 'ChannelId',
			'value' => '$data["ChannelName"]',
			),**/
		array(
		'name' => 'Description',
		'value' => '$data["Description"]',
		),
		array(
		'name' => 'Status',
		'value' => '$data["Status"]',
		),

	),	
)); ?>
