<?php
/* @var $this CampaignsController */
/* @var $model Campaigns */

$this->breadcrumbs=array(
	'Campaigns'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Campaigns', 'url'=>array('index')),
	// array('label'=>'Manage Campaigns', 'url'=>array('admin')),
);
?>

<h1>Create Campaigns</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'brand_list'=>$brand_list)); ?>