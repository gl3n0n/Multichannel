<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Scheduled Event Post' =>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Scheduled Event Posts', 'url'=>array('index')),
);
?>

<h1>Create Scheduled Event Post</h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'client_list'   =>$client_list,
	'point_list'   =>array(),
	'coupon_list'  =>array(),
	'reward_list'  =>array(),
)); 

?>
