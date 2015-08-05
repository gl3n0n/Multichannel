<?php
/* @var $this PointsLogController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Points Logs',
);

$this->menu=array(
	// array('label'=>'Create PointsLog', 'url'=>array('create/?id='.$model->PointsId)),
	// array('label'=>'Manage PointsLog', 'url'=>array('admin')),
);

?>

<h1>Points Logs</h1>

<?php $this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataProvider,
	// 'dataProvider'=>$model->search(),
	'itemView'=>'_view',
)); ?>
