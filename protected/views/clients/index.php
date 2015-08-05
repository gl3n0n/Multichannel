<?php
/* @var $this ClientsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Clients',
);

$this->menu=array(
	// array('label'=>'Create Clients', 'url'=>array('create')),
	// array('label'=>'Manage Clients', 'url'=>array('admin')),
);
?>

<h1>Clients</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
			'ClientId',
			'CompanyName',
			'Address',
			'Email',
			'Landline',
			'Status',
			'DateCreated',
			'CreatedBy',
			'DateUpdated',
			'UpdatedBy',
	),
)); ?>
