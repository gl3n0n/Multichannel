<?php
/* @var $this RewardsListController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Rewards Lists',
);

$this->menu=array(
	array('label'=>'Create RewardsList', 'url'=>array('create')),
	// array('label'=>'Manage RewardsList', 'url'=>array('admin')),
);
?>

<h1>Rewards Lists</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'RewardId',
	'Title',
	'Description',
	'Image',
	'Availability',
	'Status',
	'DateCreated',
	'CreatedBy',
	'UpdatedBy',
	),
)); ?>
