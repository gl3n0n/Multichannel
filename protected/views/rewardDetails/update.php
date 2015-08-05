<?php
/* @var $this RewardDetailsController */
/* @var $model RewardDetails */

$this->breadcrumbs=array(
	'Reward Details'=>array('index'),
	$model->RewardConfigId=>array('view','id'=>$model->RewardConfigId),
	'Update',
);

$this->menu=array(
	array('label'=>'List RewardDetails', 'url'=>array('index')),
	array('label'=>'Create RewardDetails', 'url'=>array('create')),
	array('label'=>'View RewardDetails', 'url'=>array('view', 'id'=>$model->RewardConfigId)),
	//array('label'=>'Manage RewardDetails', 'url'=>array('admin')),
);
?>

<h1>Update RewardDetails <?php echo $model->RewardConfigId; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'client_list'=>$client_list, 'brand_list'=>$brand_id, 'campaign_list'=>$campaign_id, 'channel_list'=>$channel_id, 'rewards_list'=>$rewardlist_id)); ?>