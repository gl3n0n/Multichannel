<fieldset>
	<legend>Access Type</legend>
	<?php
	echo ZHtml::enumDropDownList(Users::model(), 'AccessType',
			array(
				'id'      =>'byAccessType',
				'name'    =>'byAccessType',
				'style'   =>'width:203px;',
				'prompt'  =>'-- --',
				'options' => array(Yii::app()->request->getParam('byAccessType') => array('selected'=>true)),

				 ));
	?>
	<br/>
</fieldset>