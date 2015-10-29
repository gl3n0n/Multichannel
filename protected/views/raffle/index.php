<?php
/* @var $this RaffleController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Raffles',
);

$this->menu=array(
	array('label'=>'Create Raffle', 'url'=>array('create')),
	// array('label'=>'Manage Raffle', 'url'=>array('admin')),
);


//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN" or Yii::app()->user->AccessType === "ADMIN")
{
	$this->menu=array(
	array('label'=>'Create Raffle',   'url'=>array('create')),
	array('label'=>'Pending Raffles', 'url'=>array('pending')),
	);
}
?>

<h1>Raffles</h1>
<div>
<?php 


$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("raffle/index"),
	'method'=>'get',
)); 


include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-byname-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bypointsystem-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');
	
$this->endWidget(); 
?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
		'name' => 'RaffleId',
		'type' => 'raw',
		'value'=> 'CHtml::link($data->RaffleId,Yii::app()->createUrl("raffle/update",array("id"=>$data->primaryKey)))',
		), 
		'RaffleName',
		array(
		'name'  => 'Coupon Name',
		'value' => '($data->raffleCoupon!=null)?($data->raffleCoupon->CouponName):("")',
		),
		array(
			'name' => 'Points System Name',
			'value'=> '($data->byPoints!=null and @count($data->byPoints))?$data->byPoints[0]->Name:""',
			),	
		array(
		'name' => 'ClientId',
		'value'=> '$data->raffleClients!=null?$data->raffleClients->CompanyName:""',
		),		
		'NoOfWinners',
		'BackUp',
		'FdaNo',
		'Source',
		'DrawDate',
		'Status',
		'DateCreated',
		array(
		'name'  => 'CreatedBy',
		'value' => '$data->raffleCreateUsers->Username',
		),
		'DateUpdated',
		array(
		'name'  => 'UpdatedBy',
		'value' => '($data->raffleUpdateUsers!=null)?($data->raffleUpdateUsers->Username):("")',
		),
		),
)); ?>
