<?php
/* @var $this RewardDetailsController */
/* @var $model RewardDetails */

$this->breadcrumbs=array(
	'Reward Details'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List RewardDetails', 'url'=>array('index')),
	// array('label'=>'Manage RewardDetails', 'url'=>array('admin')),
);
?>

<h1>Create RewardDetails</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'client_list'=>$client_list, 'brand_list'=>$brand_id, 'campaign_list'=>$campaign_id, 'channel_list'=>$channel_id, 'rewards_list'=>$rewardlist_id)); ?>