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
//	array('label'=>'Breakdown of Points Gained',       'url'=>array('pointsgainbal')),
	array('label'=>'List of Campaigns Participated',   'url'=>array('campaignpart')),
	array('label'=>'List of Redemeed Rewards',         'url'=>array('redeemrewards')),
	array('label'=>'List of Redemeed Coupons',         'url'=>array('redeemcoupons')),
	array('label'=>'Customer Activity Report',         'url'=>array('customeractivity')),
	);
}
?>
<h1>Points Log History</h1>
<div>
<?php 
if($this->statusMsg != null)
{
    echo "<div class='errorSummary'><p><h5>$this->statusMsg</h5></p></div>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/ptslog"),
	'method'=>'get',
)); ?>
<?php $this->endWidget(); ?>


</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>array(
		'PointLogId',
		array(
			'name' => 'Points System Name',
			'value'=> '$data->pointlogPoints!=null?($data->pointlogPoints->Name):("")',
			),		
		'Value',
		'LogType',
		array(
			'name' => 'Action Type',
			'value'=> '$data->pointlogActiontype!=null?($data->pointlogActiontype->Name):("")',
			),	
		array(
			'name' => 'ClientId',
			'value'=> '$data->pointlogClients!=null?($data->pointlogClients->CompanyName):("")',
			),		
		array(
			'name' => 'Customer Name',
			'value'=> '$data->pointlogCustomers!=null?($data->pointlogCustomers->LastName. " " .$data->pointlogCustomers->FirstName):("")',
			),		
		array(
			'name' => 'Brand Name',
			'value'=> '$data->pointlogBrands!=null?($data->pointlogBrands->BrandName):("")',
			),	
		'DateCreated',
       ),
)); 
?>

