<?php
/* @var $this PointsLogController */
/* @var $model PointsLog */

$this->breadcrumbs=array(
	'Points Logs'=>array('index'),
	$model->PointLogId=>array('view','id'=>$model->PointLogId),
	'Update',
);

$this->menu=array(
	array('label'=>'List PointsLog', 'url'=>array('index')),
	array('label'=>'Create PointsLog', 'url'=>array('create')),
	array('label'=>'View PointsLog', 'url'=>array('view', 'id'=>$model->PointLogId)),
	array('label'=>'Manage PointsLog', 'url'=>array('admin')),
);
?>

<h1>Update PointsLog <?php echo $model->PointLogId; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>