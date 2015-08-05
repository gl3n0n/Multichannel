<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Brands',
);

$this->menu=array(
	array('label'=>'Create Brands', 'url'=>array('create')),
	//array('label'=>'Manage Brands', 'url'=>array('admin')),
);
?>

<h1>Brands</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'BrandId',
	'ClientId',
	'BrandName',
	'Description',
	'DurationFrom',
	'DurationTo',
	'Status',
	'DateCreated',
	'CreatedBy',
	'DateUpdated',
	'UpdatedBy',
	),
)); ?>
