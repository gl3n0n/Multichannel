<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Brands'=>array('index'),
	$model->BrandId,
);

$this->menu=array(
	array('label'=>'List Brands', 'url'=>array('index')),
	array('label'=>'Create Brands', 'url'=>array('create')),
	array('label'=>'Update Brands', 'url'=>array('update', 'id'=>$model->BrandId)),
	// array('label'=>'Delete Brands', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->BrandId),'confirm'=>'Are you sure you want to delete this item?')),
	// array('label'=>'Manage Brands', 'url'=>array('admin')),
);
?>

<h1>View Brands #<?php echo $model->BrandId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'BrandId',
		 array(
			'name' => 'ClientId',
			'value' => $model->clientBrands->CompanyName,
			),
		'BrandName',
		'Description',
		'DurationFrom',
		'DurationTo',
		'Status',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value' => $model->brandCreateUsers->Username,
			),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value' => $model->brandUpdateUsers->Username,
			),
	),
)); ?>
