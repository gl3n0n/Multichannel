<?php
/* @var $this ReportsController */

$this->breadcrumbs=array(
	'Reports',
);

//overwrite
if(1)
{
	$this->menu=array(
	array('label'=>'List of Campaigns Participated',   'url'=>array('campaignpart')),
	array('label'=>'List of Redemeed Rewards',         'url'=>array('redeemrewards')),
	array('label'=>'List of Redemeed Coupons',         'url'=>array('redeemcoupons')),
	array('label'=>'Customer Activity Report',         'url'=>array('customeractivity')),
	);
}
?>
<h1>Breakdown of Points Gained</h1>
<div>

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/pointsgain"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Channel Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="ChannelName" title="Search Channel Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); 

?>


</div>
<div id="yw1" class="grid-view">
<br/>
<h4>
Current Total Points: <?=@intval($dataPts)?>
</h4>
<table class="items">
	<thead>
		<tr>
			<th id="yw1_c0">Client</th>
			<th id="yw1_c1">Brand</th>
			<th id="yw1_c2">Campaign</th>
			<th id="yw1_c2">Channel</th>
			<th id="yw1_c2">Points</th>
		</tr>
	</thead>
	<tbody>
	<?php
	
	$css = 0;
	foreach($dataRes as $row)
	{
	   $css++;
	   $cssmode = ($css%2 == 1)?('odd'):('even');
	?>
	<tr class="<?=$cssmode?>">
		<td><?=$row['CLIENTS']?></td>
		<td><?=$row['BRANDS']?></td>
		<td><?=$row['CAMPAIGNS']?></td>
		<td><?=$row['CHANNELS']?></td>
		<td><?=$row['BALANCE']?></td>
	</tr>
	<?php
	}
	if($css <= 0)
	{
	?>
	<tr><td colspan="10" class="empty">
		<span class="empty">
		No results found.
		</span>
	    </td>
	</tr>
	<?php
	}?>
	</tbody>
</table>
<?php
?>
