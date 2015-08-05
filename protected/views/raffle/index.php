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
?>

<h1>Raffles</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
		'RaffleId',
		'Source',
		'NoOfWinners',
		'BackUp',
		'FdaNo',
		'DrawDate',
		'DateCreated',
		'CreatedBy',
		'DateUpdated',
		'UpdatedBy',
		'Status',
		'CouponId',
		),
)); ?>
