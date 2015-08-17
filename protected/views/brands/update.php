<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Brands'=>array('index'),
	$model->BrandId=>array('view','id'=>$model->BrandId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Brands', 'url'=>array('index')),
	array('label'=>'Create Brands', 'url'=>array('create')),
	array('label'=>'View Brands', 'url'=>array('view', 'id'=>$model->BrandId)),
	// array('label'=>'Manage Brands', 'url'=>array('admin')),
);
?>

<h1>Update <?php echo $model->BrandName; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'client_list'=>$client_list)); ?>