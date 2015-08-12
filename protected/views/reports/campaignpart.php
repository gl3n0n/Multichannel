<?php
/* @var $this CustomerSubscriptionsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Reports',
);

//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu=array(
	array('label'=>'Breakdown of Points Gained',       'url'=>array('pointsgain')),
	array('label'=>'List of Campaigns Participated',   'url'=>array('campaignpart')),
	array('label'=>'List of Redemeed Rewards',         'url'=>array('redeemrewards')),
	array('label'=>'List of Redemeed Coupons',         'url'=>array('redeemcoupons')),
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
			'value' => '$data->subsClients->CompanyName',
			),
		array(
			'name' => 'BrandId',
			'value' => '$data->subsBrands->BrandName',
			),
		array(
			'name' => 'CampaignId',
			'value' => '$data->subsCampaigns->CampaignName',
			),
		array(
			'name' => 'ChannelId',
			'value' => '$data->subsChannels->ChannelName',
			),
		array(
		'name' => 'Description',
		'value' => '$data->subsCampaigns->Description',
		),
		'Status',
	),	
)); ?>
