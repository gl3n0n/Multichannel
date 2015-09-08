<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Points System Mapping' =>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Points System Mapping', 'url'=>array('index')),
);
?>

<h1>Create Points System Mapping</h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'points_system_list'  => $points_system_list,
	'brand_list'          => $brand_list,
	'campaign_list'       => $campaign_list,
	'channel_list'        => $channel_list,

)); 

?>