<?php
/* @var $this RewardDetailsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Reward Details',
);

$this->menu=array(
	array('label'=>'Create RewardDetails', 'url'=>array('create')),
	// array('label'=>'Manage RewardDetails', 'url'=>array('admin')),
);
?>

<h1>Reward Details</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'RewardConfigId',
	'RewardId',
	'ChannelId',
	'Inventory',
	'Limitations',
	'Value',
	'Availability',
	'Status',
	'DateCreated',
	'CreatedBy',
	'DateUpdated',
	'UpdatedBy',
	),
)); ?>
