<?php
/* @var $this PointsController */
/* @var $model Points */

$this->breadcrumbs=array(
	'Points'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Points', 'url'=>array('index')),
	// array('label'=>'Manage Points', 'url'=>array('admin')),
);
?>

<h1>Create Points</h1>

<?php $this->renderPartial('_form2', array('model'=>$model, 'brand_list'=>$brand_id, 'campaign_list'=>$campaign_id, 'channel_list'=>$channel_id, 'rewards_list'=>$rewardlist_id)); ?>