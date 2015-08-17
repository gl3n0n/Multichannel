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
if(Yii::app()->user->AccessType === "SUPERADMIN")
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
)); ?>
	<fieldset>
		<legend>Search By Source</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Source" title="Search Source">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',CHtml::link(CHtml::encode($data->RaffleId), array('view', 'id'=>$data->RaffleId))
	'columns'=>array(
		//'RaffleId',
		array(
		'name' => 'RaffleId',
		'type' => 'raw',
		'value'=> 'CHtml::link($data->RaffleId,Yii::app()->createUrl("raffle/update",array("id"=>$data->primaryKey)))',
		), 
		'Source',
		'NoOfWinners',
		'BackUp',
		'FdaNo',
		array(
		'name' => 'ClientId',
		'value'=> '$data->raffleClients!=null?$data->raffleClients->CompanyName:""',
		),		
		'DrawDate',
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
		'Status',
		'CouponId',
		),
)); ?>
