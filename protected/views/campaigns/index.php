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

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
