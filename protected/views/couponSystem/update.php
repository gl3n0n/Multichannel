<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Coupon System'=>array('index'),
	$model->CouponId =>array('view','id'=>$model->CouponId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Coupon System',   'url'=>array('index')),
	array('label'=>'Create Coupon System', 'url'=>array('create')),
	array('label'=>'Update Coupon System', 'url'=>array('update', 'id'=>$model->CouponId)),
);
?>

<h1>Update  <?php echo $model->CouponName; ?></h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'points_id'   =>$points_id,
)); 

?>
