<?php
/* @var $this CampaignsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Campaigns',
);

$this->menu=array(
	array('label'=>'Create Campaigns', 'url'=>array('create')),
	// array('label'=>'Manage Campaigns', 'url'=>array('admin')),
);
?>

<h1>Campaigns</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'CampaignId',
	'ClientId',
	'BrandId',
	'CampaignName',
	'Description',
	'DurationFrom',
	'DurationTo',
	'Type',
	'Status',
	'DateCreated',
	'CreatedBy',
	'UpdatedBy',
	),
)); ?>
