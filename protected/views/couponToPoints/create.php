<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Coupon To Points' =>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Coupon To Points', 'url'=>array('index')),
);
?>

<h1>Create Coupon To Points</h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'coupon_list'   =>$coupon_list,
)); 

?>