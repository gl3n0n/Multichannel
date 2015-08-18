<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Scheduled Post'=>array('index'),
	$model->SchedId =>array('view','id'=>$model->SchedId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Scheduled Post',   'url'=>array('index')),
	array('label'=>'Create Scheduled Post', 'url'=>array('create')),
	array('label'=>'Update Scheduled Post', 'url'=>array('update', 'id'=>$model->SchedId)),
	array('label'=>'Delete Scheduled Post', 'url'=>'#', 
		'linkOptions' => array('submit'=> array('delete','id'=>$model->SchedId),
		'confirm'=>'Are you sure you want to delete this item?')),	

);
?>

<h1>Update Scheduled Post <?php echo $model->SchedId; ?></h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'client_list'   =>$client_list,
	'brand_list'    =>$brand_list,
	'campaign_list' =>$campaign_list,
	'channel_list'  =>$channel_list,
	'point_list'   =>$point_list,
	'coupon_list'  =>$coupon_list,
	'reward_list'  =>$reward_list,
)); 

?>