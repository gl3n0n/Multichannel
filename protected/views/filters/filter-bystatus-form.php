<fieldset>
	<legend>Status</legend>
	<?php
	echo ZHtml::enumDropDownList(Clients::model(), 'Status',
			array(
				'id'      =>'byStatusType',
				'name'    =>'byStatusType',
				'style'   =>'width:203px;',
				'prompt'  =>'-- --',
				'options' => array(Yii::app()->request->getParam('byStatusType') => array('selected'=>true)),

				 ));
	?>
	<br/>
</fieldset>