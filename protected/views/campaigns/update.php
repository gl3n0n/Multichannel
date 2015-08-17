<?php
/* @var $this CampaignsController */
/* @var $model Campaigns */

$this->breadcrumbs=array(
	'Campaigns'=>array('index'),
	$model->CampaignId=>array('view','id'=>$model->CampaignId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Campaigns', 'url'=>array('index')),
	array('label'=>'Create Campaigns', 'url'=>array('create')),
	array('label'=>'View Campaigns', 'url'=>array('view', 'id'=>$model->CampaignId)),
	// array('label'=>'Manage Campaigns', 'url'=>array('admin')),
);
?>

<h1>Update <?php echo $model->CampaignName; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'brand_list'=>$brand_list)); ?>