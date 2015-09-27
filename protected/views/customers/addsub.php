<?php
/* @var $this CustomersController */
/* @var $model Customers */

$this->breadcrumbs=array(
	'Customers'=>array('index'),
	($CustomerId?$CustomerId:0),
);
$this->menu=array(
	array('label'=>'Add/Deduct Points', 'url'=>array('addsub', 'id'=>($CustomerId?$CustomerId:0))),
);

if(0)
{
echo "<pre>$CustomerId=OKS# " .@var_export($brand_list,1)."</pre>";
echo "<pre>$CustomerId=OKS# " .@var_export($campaign_list,1)."</pre>";
echo "<pre>$CustomerId=OKS# " .@var_export($point_list,1)."</pre>";
echo "<pre>$CustomerId=OKS# " .@var_export($model,1)."</pre>";
exit;
}

?>

<h1>Add/Deduct Points</h1>

<?php $this->renderPartial('_addsub', 
		array(
		'model'         => $model,
		'CustomerId'    => $CustomerId,
		'brand_list'    => $brand_list,
		'campaign_list' => $campaign_list,
		'point_list'    => $point_list,
		'client_list'   => $client_list,
		)); 
?>