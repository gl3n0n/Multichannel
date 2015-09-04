<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Convert Points to Coupon' =>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Convert Points to Coupon', 'url'=>array('index')),
);
?>

<h1>Create Convert Points to Coupon</h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'coupon_list'   =>$coupon_list,
)); 

?>