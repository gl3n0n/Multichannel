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

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
