<fieldset class='filterSrch'>
<legend>Date (From)</legend>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
	'model'=>Clients::model(),
	'value'=>Yii::app()->request->getParam('byTranDateFr'),
	'id'   =>'byTranDateFr',
	'name' =>'byTranDateFr',
	'options' => array(
		'showAnim'        => "slideDown",
		'changeMonth'     => true,
		'numberOfMonths'  => 1,
		'showOn'          => "button",
		'buttonImageOnly' => false,
		'dateFormat'      => "yy-mm-dd",
		'showButtonPanel' => true      
	),
	'htmlOptions'=>array(
			'style'=>'width:170px;',
		),				
));
?>
<br/>
</fieldset>
