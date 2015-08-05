<?php
/* @var $this ChannelsController */
/* @var $model Channels */

$this->breadcrumbs=array(
	'Channels'=>array('index'),
	$model->ChannelId=>array('view','id'=>$model->ChannelId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Channels', 'url'=>array('index')),
	array('label'=>'Create Channels', 'url'=>array('create')),
	array('label'=>'View Channels', 'url'=>array('view', 'id'=>$model->ChannelId)),
	array('label'=>'Manage Channels', 'url'=>array('admin')),
);
?>

<h1>Update Channels <?php echo $model->ChannelId; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'brand_id'=>$brand_id, 'campaign_id'=>$campaign_id)); ?>