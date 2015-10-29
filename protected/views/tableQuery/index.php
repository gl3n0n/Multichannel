<?php
/* @var $this ReportsController */

$this->breadcrumbs=array(
	'Table Query',
);


//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu=array(
		array('label'=>'Table Query Report', 'url'=>array('index')),
	);
}

?>
<script>
function downloadCSV(csvPath) 
{
	var iframe;
	iframe = document.getElementById("csvdownloader");
	if (iframe == null) {
		iframe = document.createElement('iframe');
		iframe.id = "csvdownloader";
		iframe.style.visibility = 'hidden';
		document.body.appendChild(iframe);
	}
	iframe.src = csvPath;
	return true;
}
$( document ).ready(function() {
    
	
	//$("#reportFilter").show();
    $("#DIVFILTER").click(function(){
	$("#reportFilter").toggle();
    });


	$("#byTableName").change(function(){
		getListings();
	});
	
	function getListings()
	{
	    var str = $("#byTableName" ).val();
	    var url = BaseUrl + "tableQuery/getcolumns/?colname=" + str;
	    loadlist($('select#tableFilters').get(0),
				 $('select#tableCols').get(0),
				url,
				''
			     );
	}
	
	//add it
	function loadlist(selobj1,selobj2,url,nameattr)
	{
	    $(selobj1).empty();
	    $(selobj1).append(
		$('<option></option>')
		.val('')
		.html('-- Please Select --'));
		
		$(selobj2).empty();
	    
	    $.getJSON(url,{},function(data)
	    {
	        $.each(data, function(i,obj)
	        {
	            $(selobj1).append(
	                 $('<option></option>')
	                        .val(i)
	                        .html(obj));
				$(selobj2).append(
	                 $('<option></option>')
	                        .val(i)
	                        .html(obj));							
	        });
	    });
	}	
	//load initially
	getListings();
});
</script>
<h1>Table Queries</h1>
<fieldset class='filterSrchBold'>
	<legend id='DIVFILTER'>
	<h3>
	Search Filter(s)
	</h3>
	</legend>
<div id='reportFilter'>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("tableQuery/query"),
	'method'=>'get',
)); ?>

	<fieldset class='filterSrch'>
	<legend>Table Name(s)</legend>
		<?php 
		echo CHtml::dropDownList('byTableName', 
		Yii::app()->request->getParam('byTableName'), 
		$Tables,
		array(
        	    'style'    => 'width:200px;',
        	    'prompt'   => '-- Pls Select --',
        	),
		array('empty' => '-- Pls Select --')
		);
		?>
	</fieldset>
	<fieldset class='filterSrch'>
		<legend>Client Name</legend>
		<input type="text" id='byClientName' 
		 style="width:200px;"
		 name="byClientName" 
		 placeholder="ClientName" 
		 title="Search Client Name"
		 value="<?=Yii::app()->request->getParam('byClientName')?>"
		 />
	</fieldset>
	<fieldset class='filterSrch'>
	<legend>Transaction Date (From)</legend>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model'=>$model,
                'value'=>Yii::app()->request->getParam('byTranDateFr'),
				'id'   =>'byTranDateFr',
		        'name' =>'byTranDateFr',
				'options' => array(
					'showAnim' => "slideDown",
					'changeMonth' => true,
					'numberOfMonths' => 1,
					'showOn' => "button",
					'buttonImageOnly' => false,
					'dateFormat' => "yy-mm-dd",
					'showButtonPanel' => true      
       			),
				'htmlOptions'=>array(
						'style'=>'width:170px;',
					),				
           	));
       	?>
		<br/>
	</fieldset>
	<fieldset class='filterSrch'>
		<legend>Transaction Date (To)</legend>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model'=>$model,
                'value'=>Yii::app()->request->getParam('byTranDateTo'),
				'id'   =>'byTranDateTo',
		        'name' =>'byTranDateTo',
				'options' => array(
					'showAnim' => "slideDown",
					'changeMonth' => true,
					'numberOfMonths' => 1,
					'showOn' => "button",
					'buttonImageOnly' => false,
					'dateFormat' => "yy-mm-dd",
					'showButtonPanel' => true      
       			),
				'htmlOptions'=>array(
						'style'=>'width:170px;',
					),				
				
           	));
       	?>
		<br/>
	</fieldset>

	<fieldset class='filterSrch'>
		<legend>Filter These Columns</legend>
		<select id='tableFilters' name='tableFilters' style='width:200px;'>
		  <option value="">- -</option>
		</select>
	</fieldset>	
	<fieldset class='filterSrch'>
		<legend>Filter Column</legend>
		<input type="text" id='byFilterName' 
		 style="width:200px;"
		 name="byFilterName" 
		 placeholder="Filter Column" 
		 title="Search by"
		 value="<?=Yii::app()->request->getParam('byFilterName')?>"
		 />
	</fieldset>	
	<fieldset class='filterSrch'>
		<legend>Show These Columns</legend>
		<select multiple id='tableCols' name='tableCols[]' style='width:200px;height:200px;'>
		  <option value="">- -</option>
		</select>
	</fieldset>	
	
	<fieldset class='filterSrch'>
		<br/>
		<button type="submit" id='btnQuery' style="width:200px;">
		Query
		</button>
		<br/>
		<br/>
	</fieldset>	
<?php $this->endWidget(); ?>
</div>
<?php 
if(!empty($downloadCSV))
{

?>
	<div>
	<fieldset class='filterSrch'>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl("tableQuery/index"),
		'method'=>'get',
	)); ?>
		<fieldset class='filterSrch'>
			<legend>CSV</legend>
			<a href="#" onclick="downloadCSV('<?php echo Yii::app()->createUrl("reportsList/csv")?>/?fn=<?php echo $downloadCSV?>');">
			DOWNLOAD CSV 
			</a>
		</fieldset>
		<br/>
		<br/>
		<iframe id="csvdownloader" style="display:none"
		 width=0 height=0 style="hidden" frameborder=0 marginheight=0 marginwidth=0 scrolling=no></iframe>
	<?php $this->endWidget(); ?>
	</fieldset>
</div>
<?php
}//show download
?>
</div>
<?php
if($byTableNameX == 'clients'){
?>
<h4>CLIENTS:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //clients
?>
<?php
if($byTableNameX == 'customers'){
?>
<h4>CUSTOMERS:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //customers
?>
<?php
if($byTableNameX == 'users'){
?>
<h4>USERS:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //users
?>
<?php
if($byTableNameX == 'brands'){
?>
<h4>BRANDS:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //brands
?>
<?php
if($byTableNameX == 'campaigns'){
?>
<h4>CAMPAIGNS:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //campaigns
?>
<?php
if($byTableNameX == 'channels'){
?>
<h4>CHANNELS:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //channels
?>
<?php
if($byTableNameX == 'points'){
?>
<h4>Points System:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //points
?>
<?php
if($byTableNameX == 'points_mapping'){
?>
<h4>Points System Mapping:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //Points System Mapping
?>
<?php
if($byTableNameX == 'action_type'){
?>
<h4>Action Type:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //Action Type
?>
<?php
if($byTableNameX == 'rewards_list'){
?>
<h4>Rewards List:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //Rewards List
?>
<?php
if($byTableNameX == 'reward_details'){
?>
<h4>Rewards and Redemption:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //Rewards and Redemption
?>


<?php
if($byTableNameX == 'coupon'){
?>
<h4>Coupon System:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //Coupon System
?>



<?php
if($byTableNameX == 'coupon_to_points'){
?>
<h4>Coupon To Points:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //Coupon To Points
?>


<?php
if($byTableNameX == 'raffle'){
?>
<h4>Raffles:</h4>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>$showColumns,
)); 
?>
<?php
 } //Raffles
?>