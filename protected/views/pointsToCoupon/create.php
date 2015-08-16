<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Coupon on Points' =>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Coupon on Points', 'url'=>array('index')),
);
?>

<h1>Create Coupon on Points</h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'coupon_list'   =>$coupon_list,
)); 

?>