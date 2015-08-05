<?php
/* @var $this RaffleController */
/* @var $model Raffle */

$this->breadcrumbs=array(
	'Raffles'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Raffle', 'url'=>array('index')),
	// array('label'=>'Manage Raffle', 'url'=>array('admin')),
);
?>

<h1>Create Raffle</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'coupon_list'=>$coupon_id)); ?>