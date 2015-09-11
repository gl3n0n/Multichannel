<?php
/* @var $this RaffleController */
/* @var $model Raffle */

$this->breadcrumbs=array(
	'Raffles'=>array('index'),
	$model->RaffleId=>array('view','id'=>$model->RaffleId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Raffle', 'url'=>array('index')),
	array('label'=>'Create Raffle', 'url'=>array('create')),
	array('label'=>'View Raffle', 'url'=>array('view', 'id'=>$model->RaffleId)),
	// array('label'=>'Manage Raffle', 'url'=>array('admin')),
);
?>

<h1>Update <?php echo $model->RaffleName; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'coupon_list'=>$coupon_id)); ?>