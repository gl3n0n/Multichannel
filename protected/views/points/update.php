<?php
/* @var $this PointsController */
/* @var $model Points */

$this->breadcrumbs=array(
	'Points'=>array('index'),
	$model->PointsId=>array('view','id'=>$model->PointsId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Points', 'url'=>array('index')),
	array('label'=>'Create Points', 'url'=>array('create')),
	array('label'=>'View Points', 'url'=>array('view', 'id'=>$model->PointsId)),
	// array('label'=>'Manage Points', 'url'=>array('admin')),
);
?>

<h1>Update Points <?php echo $model->PointsId; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'rewards_list'=>$rewardlist_id)); ?>