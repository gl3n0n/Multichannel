<?php
/* @var $this PointsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Points',
);

$this->menu=array(
	array('label'=>'Create Points', 'url'=>array('create')),
	//array('label'=>'Manage Points', 'url'=>array('admin')),
);
?>

<h1>Points</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'PointsId',
	'ClientId',
	'BrandId',
	'CampaignId',
	'ChannelId',
	'From',
	'To',
	'Value',
	'PointAction',
	'PointsLimit',
	'PointCapping',
	'Status',
	'DateCreated',
	'CreatedBy',
	'UpdatedBy',
	),
)); ?>
