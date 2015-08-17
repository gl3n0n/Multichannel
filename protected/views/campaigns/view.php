<?php
/* @var $this CampaignsController */
/* @var $model Campaigns */

$this->breadcrumbs=array(
	'Campaigns'=>array('index'),
	$model->CampaignId,
);

$this->menu=array(
	array('label'=>'List Campaigns', 'url'=>array('index')),
	array('label'=>'Create Campaigns', 'url'=>array('create')),
	array('label'=>'Update Campaigns', 'url'=>array('update', 'id'=>$model->CampaignId)),
	// array('label'=>'Delete Campaigns', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->BrandId),'confirm'=>'Are you sure you want to delete this item?')),
	// array('label'=>'Manage Campaigns', 'url'=>array('admin')),
);
?>

<h1>View <?php echo $model->CampaignName; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'CampaignId',
		array(
			'name' => 'ClientId',
			'value' => $model->campaignClients->CompanyName,
			),
		array(
			'name' => 'BrandId',
			'value' => $model->campaignBrands->BrandName,
			),
		'CampaignName',
		'Description',
		'DurationFrom',
		'DurationTo',
		'Type',
		'Status',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value' => $model->campaignCreateUsers->Username,
			),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value' => $model->campaignUpdateUsers->Username,
			),
	),
)); ?>
