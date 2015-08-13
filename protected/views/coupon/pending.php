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
if(0){
foreach($dataProvider->getData() as $row)
{
echo "<hr>";
echo @var_export($row->couponMap[0]->couponBrands,true);
}
exit;
}

$this->widget('CGridViewEtc', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'etc' => $mapping,
	'columns'=>array(
	array(
	'name' => 'CouponId',
	'type' => 'raw',
	'value'=> 'CHtml::link($data->CouponId,Yii::app()->createUrl("coupon/view",array("id"=>$data->primaryKey)))',
	), 
	'Quantity',
	'LimitPerUser',
	'Source',
	array(
	'name'  => 'Brands',
	//'value' => '$this->grid->etc[\'Brands\'][$data->couponMap[0]->BrandId]',
	'value' => '($data->couponMap[0]->couponBrands != null )?$data->couponMap[0]->couponBrands->BrandName:("")',
	),
	array(
		'name'  => 'Channels',
	'value' => '($data->couponMap[0]->couponChannels != null )?$data->couponMap[0]->couponChannels->ChannelName:("")',
	),
	array(
		'name'  => 'Campaigns',
		'value' => '($data->couponMap[0]->couponCampaigns != null )?$data->couponMap[0]->couponCampaigns->CampaignName:("")',
	),	
	array(
		'name' => 'Action',
		'type' => 'raw',
		'value'=> '($data->Status !== "PENDING")?(""):(CHtml::link("Approve",Yii::app()->createUrl("coupon/approve/",array("uid"=>$data->primaryKey))))'
		), 
	
	),	
)); ?>
