<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Scheduled Event Post'=>array('index'),
	$model->SchedId =>array('view','id'=>$model->SchedId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Scheduled Event Post',   'url'=>array('index')),
	array('label'=>'Create Scheduled Event Post', 'url'=>array('create')),
	array('label'=>'Update Scheduled Event Post', 'url'=>array('update', 'id'=>$model->SchedId)),
	array('label'=>'Delete Scheduled Event Post', 'url'=>'#', 
		'linkOptions' => array('submit'=> array('delete','id'=>$model->SchedId),
		'confirm'=>'Are you sure you want to delete this item?')),	

);
?>

<h1>Update Scheduled Event Post <?php echo $model->SchedId; ?></h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'client_list'   =>$client_list,
	'brand_list'    =>array(),
	'campaign_list' =>array(),
	'channel_list'  =>array(),
	'point_list'   =>$point_list,
	'coupon_list'  =>$coupon_list,
	'reward_list'  =>$reward_list,
)); 

?>
