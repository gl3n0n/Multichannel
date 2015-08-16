<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Coupon To Points'=>array('index'),
	$model->CtpId=>array('view','id'=>$model->CtpId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Coupon To Points',   'url'=>array('index')),
	array('label'=>'Create Coupon To Points', 'url'=>array('create')),
	array('label'=>'Update Coupon To Points', 'url'=>array('update', 'id'=>$model->CtpId)),
);
?>

<h1>Update Coupon To Points <?php echo $model->CtpId; ?></h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'coupon_list'   =>$coupon_list,
)); 

?>