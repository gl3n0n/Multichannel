<?php
if(Yii::app()->user->AccessType === 'SUPERADMIN' )
{
?>
	<fieldset>
		<legend>Client Name</legend>
		<?php
				$clients = Clients::model()->active()->findAll(array('select' => 'ClientId, CompanyName'));
				$clientz = CHtml::listData($clients, 'ClientId', 'CompanyName');
				echo $form->dropDownList(Clients::model(),
					'ClientId',
					$clientz,
					array(
						'style'    => 'width:203px;',
						'id'       => 'byClient' ,
						'options'  => array(((!empty($_REQUEST['Clients']))?($_REQUEST['Clients']['ClientId']):(0)) => array('selected'=>true)),
						'prompt'   => '-- Pls Select --',
					),
					array('empty' => '-- Pls Select --'));
					?>
		<br/>
	</fieldset>
<?php
}//if super-admin
?>
