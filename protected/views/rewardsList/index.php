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

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
