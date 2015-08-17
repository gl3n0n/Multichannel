<?php
/* @var $this RaffleController */
/* @var $model Raffle */

$this->breadcrumbs=array(
	'Raffles'=>array('index'),
	$model->RaffleId,
);

$this->menu=array(
	array('label'=>'List Raffle', 'url'=>array('index')),
	array('label'=>'Create Raffle', 'url'=>array('create')),
	array('label'=>'Update Raffle', 'url'=>array('update', 'id'=>$model->RaffleId)),
	//array('label'=>'Delete Raffle', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->RaffleId),'confirm'=>'Are you sure you want to delete this item?')),
	//array('label'=>'Manage Raffle', 'url'=>array('admin')),
);
?>

<h1>View Raffle #<?php echo $model->RaffleId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'RaffleId',
		'Source',
		'NoOfWinners',
		'BackUp',
		'FdaNo',
		array(
		'name' => 'ClientId',
		'value'=> $model->raffleClients!=null?$model->raffleClients->CompanyName:"",
		),
		'DrawDate',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value' => $model->raffleCreateUsers->Username,
			),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value' => $model->raffleUpdateUsers->Username,
			),
		'Status',
		'CouponId',
	),
)); ?>
