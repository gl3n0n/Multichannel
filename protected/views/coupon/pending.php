<?php
/* @var $this CouponController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Coupons',
);

$this->menu=array(
	array('label'=>'Create Coupon', 'url'=>array('create')),
);

//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu=array(
	array('label'=>'Create Coupon',   'url'=>array('create')),
	array('label'=>'Pending Coupons', 'url'=>array('pending')),
	);
}
?>

<h1>Pending Coupons</h1>
<div>
<?php 
if($this->statusMsg != null)
{
    echo "<h5 style='color:red'>$this->statusMsg</h5>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("coupon/pending"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search By Source</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Source" title="Search Source">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php 

echo "<hr>".@var_export($dataProvider->getData(),true) ;
exit;

$this->widget('CGridViewEtc', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'etc' => $mapping,
	'columns'=>array(
	array(
	'name' => 'CouponId',
	'type' => 'raw',
	'value'=> 'CHtml::link($data->CouponId,Yii::app()->createUrl("coupon/approve",array("id"=>$data->primaryKey)))',
	), 
	'Quantity',
	'LimitPerUser',
	'Source',
	array(
	'name'  => 'Brands',
	//'value' => '$this->grid->etc[\'Brands\'][$data->couponMap[0]->BrandId]',
	'value' => '($data->couponBrands != null && @count($data->couponBrands))?$data->couponBrands[0]->BrandName:("")',
	),
	array(
		'name'  => 'Channels',
	'value' => '($data->couponChannels != null && @count($data->couponChannels))?$data->couponChannels[0]->ChannelName:("")',
	),
	array(
		'name'  => 'Campaigns',
		'value' => '($data->couponCampaigns != null && @count($data->couponCampaigns))?$data->couponCampaigns[0]->CampaignName:("")',
	),	
	array(
		'name' => 'Action',
		'type' => 'raw',
		'value'=> '($data->Status !== "PENDING")?(""):(CHtml::link("Approve",Yii::app()->createUrl("coupon/approve/",array("uid"=>$data->primaryKey))))'
		), 
	
	),	
)); ?>
