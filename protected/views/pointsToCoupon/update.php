<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Convert Points to Coupon'=>array('index'),
	$model->PtcId=>array('view','id'=>$model->PtcId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Convert Points to Coupon',   'url'=>array('index')),
	array('label'=>'Create Convert Points to Coupon', 'url'=>array('create')),
	array('label'=>'View Convert Points to Coupon',   'url'=>array('view', 'id'=>$model->PtcId)),
);
?>

<h1>Update <?php echo $model->Title; ?></h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'coupon_list'   =>$coupon_list,
)); 

?>