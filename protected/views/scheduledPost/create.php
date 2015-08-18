<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Scheduled Post' =>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Scheduled Posts', 'url'=>array('index')),
);
?>

<h1>Create Scheduled Post</h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'client_list'   =>$client_list,
	'brand_list'    =>$brand_list,
	'campaign_list' =>array(),
	'channel_list'  =>array(),
	'point_list'   =>array(),
	'coupon_list'  =>array(),
	'reward_list'  =>array(),
)); 

?>