<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Coupon System' =>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Coupon System', 'url'=>array('index')),
);
?>

<h1>Create Coupon System</h1>

<?php 
$this->renderPartial('_form', array(
	'model'	      =>$model, 
	'points_id'   =>$points_id,
)); 

?>
