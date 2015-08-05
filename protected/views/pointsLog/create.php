<?php
/* @var $this PointsLogController */
/* @var $model PointsLog */

$this->breadcrumbs=array(
	'Points Logs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List PointsLog', 'url'=>array('index')),
	array('label'=>'Manage PointsLog', 'url'=>array('admin')),
);
?>

<h1>Create PointsLog</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>