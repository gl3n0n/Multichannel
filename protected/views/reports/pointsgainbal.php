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
	array('label'=>'Breakdown of Points Gained',       'url'=>array('pointsgainbal')),
	array('label'=>'List of Campaigns Participated',   'url'=>array('campaignpart')),
	array('label'=>'List of Redemeed Rewards',         'url'=>array('redeemrewards')),
	array('label'=>'List of Redemeed Coupons',         'url'=>array('redeemcoupons')),
	);
}
?>

<h1>Breakdown of Points Gained</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reports/pointsgainbal"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Channel Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="ChannelName" title="Search Channel Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<br/>
<br/>

<?php
$sumall = 0;
$dataProvider->setPagination(false);
foreach($dataProvider->getData() as $kk )
{
	$sumall += ($kk->mapPoints != null)?($kk->mapPoints->Balance):(0);
}
echo "<h2>Current Total Points: $sumall</h3>";
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
		array(
			'name' => 'ClientId',
			'value' => '$data->mapClients->CompanyName',
			),
		array(
			'name' => 'BrandId',
			'value' => '$data->mapBrands->BrandName',
			),
		array(
			'name'  => 'CampaignId',
			'value' => '$data->mapCampaigns->CampaignName',
		),			
		array(
			'name' => 'ChannelId',
			'value' => '$data->mapChannels->ChannelName',
			),
		array(
		'name'  => 'Points',
		'value' => '$data->mapPoints->Balance',
		),			
		'Status',
	),	
)); ?>
