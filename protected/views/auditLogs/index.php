<?php
/* @var $this ReportsController */

$this->breadcrumbs=array(
	'Audit Logs',
);


//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu=array(
	array('label'=>'View Audit Logs', 'url'=>array('index')),
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
    $("#DIVFILTER").click(function(){
	$("#reportFilter").toggle();
    });
});
</script>
<h1>Audit Logs</h1>
<fieldset class='filterSrchBold'>
	<legend id='DIVFILTER'>
	<h3>Search Filter</h3>
	</legend>
	<br/>
<div id='reportFilter'>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("auditLogs/index"),
	'method'=>'get',
)); ?>
	
	<fieldset class='filterSrch'>
	<table class="detail-view" id="yw0" style="width:400px;padding:2px;2px;2px;2px;">
		<tr class="even">
			<th style="200px;">
			 Date Range (From)
			</th>
			<td style="200px;">
				<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'name'        => 'byDateFr',
				'attribute'   => 'byDateFr',
				'value'       => Yii::app()->request->getParam('byDateFr'),
				'htmlOptions' => array(
					'size'        => '15',// textField size
					'maxlength'   => '10',// textField maxlength
					'placeholder' => 'Date From',// textField maxlength
					'style'       => 'width:173px;',

				),
				// additional javascript options for the date picker plugin
				'options'     => array(
				'showAnim'    => "slideDown",
				'changeMonth' => true,
				'numberOfMonths' => 1,
				'showOn'          => "button",
				'buttonImageOnly' => false,
				'dateFormat'      => "yy-mm-dd",
				'showButtonPanel' => true,
				)
			));	
				?>
			</td>
		</tr>		
		<tr class="odd">
			<th style="200px;">
			 Date Range (To)
			</th>
			<td style="200px;">
			<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'name'        => 'byDateTo',
				'attribute'   => 'byDateTo',
				'value'       => Yii::app()->request->getParam('byDateTo'),
				'htmlOptions' => array(
					'size'        => '15',// textField size
					'maxlength'   => '10',// textField maxlength
					'placeholder' => 'Date To',// textField maxlength
					'style'       => 'width:173px;',
				),
				// additional javascript options for the date picker plugin
				'options'     => array(
				'showAnim'    => "slideDown",
				'changeMonth' => true,
				'numberOfMonths' => 1,
				'showOn'          => "button",
				'buttonImageOnly' => false,
				'dateFormat'      => "yy-mm-dd",
				'showButtonPanel' => true,
				)
			));	
			?>
			</td>
		</tr>				
		<tr class="even">
			<th style="200px;">
			 Client Name
			</th>
			<td style="200px;">
				<?php
				$xtype = Yii::app()->request->getParam('byClientId');
				echo CHtml::dropDownlist('byClientId','', 
					$clientlists, 
					array(
						'id'      => 'byClientId',
						'style'   => 'width:203px;',
						'prompt'  => '-- Pls Select --',
						'options' => array("$xtype" => array('selected' => true) ),
					));
					
				?>				
			</td>
		</tr>
		
		<tr class="even">
			<th style="200px;">
			 User Name
			</th>
			<td style="200px;">
				<?php
				$xtype = Yii::app()->request->getParam('byUserName');
				echo CHtml::dropDownlist('byUserName','', 
					$usernamelist, 
					array(
						'id'      => 'byUserName',
						'style'   => 'width:203px;',
						'prompt'  => '-- Pls Select --',
						'options' => array("$xtype" => array('selected' => true) ),
					));
					
				?>				
			</td>
		</tr>
		<tr class="odd">
			<th style="200px;">
			 User Type
			</th>
			<td style="200px;">
				<?php
				$xtype = Yii::app()->request->getParam('byUserType');
				echo CHtml::dropDownlist('byUserType','', 
					$usertypelist, 
					array(
						'id'      => 'byUserType',
						'style'   => 'width:203px;',
						'prompt'  => '-- Pls Select --',
						'options' => array("$xtype" => array('selected' => true) ),
					));
					
				?>
			</td>
		</tr>		
		
		<tr class="even">
			<th style="200px;">
			 Module
			</th>
			<td style="200px;">
				<?php
				$xtype = Yii::app()->request->getParam('byModule');
				echo CHtml::dropDownlist('byModule','', 
					$usermodulelist, 
					array(
						'id'      => 'byModule',
						'style'   => 'width:203px;',
						'prompt'  => '-- Pls Select --',
						'options' => array("$xtype" => array('selected' => true) ),
					));
					
				?>
		 		
			</td>
		</tr>		
		<tr class="odd">
		<th style="200px;">
		 	&nbsp;
		</th>
		<td style="200px;">
			<button type="submit" id='btnSearch' style="width:200px;">
				Search
			</button>
		</td>
		</tr>			
		<?php 
		if(!empty($downloadCSV))
		{
		?>
		<tr class="even">
			<th style="200px;">
			 CSV
			</th>
			<td style="200px;">
				<fieldset class='filterSrch'>
					<a href="#" onclick="downloadCSV('<?php echo Yii::app()->createUrl("auditLogs/csv")?>/?fn=<?php echo $downloadCSV?>');">
					DOWNLOAD CSV 
					</a>
				</fieldset>
				<iframe id="csvdownloader" style="display:none"
	 					width=0 height=0 style="hidden" frameborder=0 marginheight=0 marginwidth=0 scrolling=no></iframe>
		 		
			</td>
		</tr>
		<?php }?>
	</table>
	
	

	</fieldset>	
	
<?php $this->endWidget(); ?>
</div>


</div>
</fieldset>
<?php $this->widget('zii.widgets.grid.CGridView', array(
       'dataProvider'=>$dataProvider,
       'columns' => array(
		array(
		'name'  => 'AuditId',
		'value' => 'CHtml::link($data->AuditId,Yii::app()->createUrl("auditLogs/view",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
		),
		array(
		'name'  => 'LogDate',
		'value' => '($data->LogDate != null)?(substr($data->LogDate,0,10)):("")',
		'type'  => 'raw',
		),
		array(
		'name'  => 'LogTime',
		'value' => '($data->LogDate != null)?(substr($data->LogDate,11)):("")',
		'type'  => 'raw',
		),
		array(
			'name'  => 'UserId',
			'value' => 'CHtml::link((($data->byUsers!=null)?($data->byUsers->Username):("")),Yii::app()->createUrl("users/view",array("id"=>$data->UserId)))',
			'type'  => 'raw',
		),
		'UserType',
		'IPAddr',
		array(
		'name'  => 'UserAgent',
		'value' => '($data->UserAgent!= null)?(nl2br($data->UserAgent)):("")',
		'type'  => 'raw',
		),		
		array(
		'name'  => 'ClientId',
		'value' => '($data->byClients != null)?($data->byClients->CompanyName):("")',
		'type'  => 'raw',
		),		
		array(
		'name'  => 'Module',
		'value' => '($data->ModPage != null)?(nl2br($data->ModPage)):("")',
		'type'  => 'raw',
		),		
		array(
		'name'  => 'Action',
		'value' => '($data->ModAction != null)?(nl2br($data->ModAction)):("")',
		'type'  => 'raw',
		),		
        
    ),
)); ?>
