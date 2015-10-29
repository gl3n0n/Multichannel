<?php
/* @var $this BrandsController */
/* @var $model Brands */
/* @var $form CActiveForm */
if($model->scenario === 'insert')
{
   echo Yii::app()->params['jQueryInclude'];
}


?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'my-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    <?php if(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert'): ?>
    <div class="row">
        <?php echo $form->labelEx($model,'ClientId'); ?>
        <?php echo $form->dropDownList($model,'ClientId',$client_list,
        	array(
        	    'style'     => 'width:200px;',
        	    'prompt'    => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
        <?php echo $form->error($model,'ClientId'); ?>
    </div>
    <?php endif; ?>


	<div class="row">
		<?php echo $form->labelEx($model,'Title'); ?>
		<?php echo $form->textField($model,'Title',array(
		'style'    => 'width:200px;',
		'maxlength'=>255
		)); 
		?>
		<?php echo $form->error($model,'Title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Description'); ?>
		<?php echo $form->textField($model,'Description',array(
			'style' => 'width:200px;',
			'maxlength'=>255
		));
		?>
		<?php echo $form->error($model,'Description'); ?>
	</div>

	<div class="row">
		<?php 
			$rlist  = $model->getDropDownList();
			$ntype  = $rlist["AwardName"] ?$rlist["AwardName"] :array();
			$atype  = $rlist["AwardType"] ?$rlist["AwardType"] :array();
		?>
		<?php echo $form->labelEx($model,'Grouping Type'); ?>
		<?php echo $form->dropDownList($model,'AwardName',$ntype,
		array(
        	    'style'    => 'width:200px;',
        	    'options'  => array("$model->AwardName" => array('selected'=>true)),
        	    'prompt'   => '-- Pls Select --',
        	    'onchange' => 'hideRanges()',
        	    'onclick'  => 'hideRanges()',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'AwardName'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'Value'); ?>
		<?php echo $form->textField($model,'Value',array(
		'style'    => 'width:200px;',
		'maxlength'=> 11
		)); 
		?>
		<?php echo $form->error($model,'Value'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'AwardType'); ?>
		<?php echo $form->dropDownList($model,'AwardType',$atype,
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->AwardType" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'AwardType'); ?>
	</div>	

	<div class="row" id='dStartDate' name='dStartDate'>
		<?php echo $form->labelEx($model,'StartDate'); ?>
		<?php
	    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name'  => 'StartDate',
           'value' => substr($model->StartDate,0,10),
	   'model' =>$model,
	   'attribute'=>'StartDate',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
           )
       ));	?>
		<?php echo $form->error($model,'StartDate'); ?>
	</div>
	<div class="row" id='dEndDate' name='dEndDate'>
		<?php echo $form->labelEx($model,'EndDate'); ?>
		<?php
	    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name'  => 'EndDate',
           'value' => substr($model->EndDate,0,10),
	   'model' =>$model,
	   'attribute'=>'EndDate',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
           )
       ));	?>
		<?php echo $form->error($model,'EndDate'); ?>
	</div>


	<div class="row" id='dPointsId' name='dPointsId'>
		<?php echo $form->labelEx($model,'PointsId'); ?>
		<?php echo $form->dropDownList($model,'PointsId',$point_list?$point_list:array(),
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->PointsId" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'PointsId'); ?>
	</div>
	<div class="row" id='dCouponId' name='dCouponId'>
		<?php echo $form->labelEx($model,'CouponId'); ?>
		<?php echo $form->dropDownList($model,'CouponId',$coupon_list?$coupon_list:array(),
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->CouponId" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'CouponId'); ?>
	</div>

	<div class="row" id='dRewardId' name='RewardId' >
		<?php echo $form->labelEx($model,'RewardId'); ?>
		<?php echo $form->dropDownList($model,'RewardId',$reward_list?$reward_list:array(),
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->RewardId" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'RewardId'); ?>
	</div>

    <div class="row">
			<?php echo $form->labelEx($model,'Status'); ?>
			<?php echo CHtml::dropDownList('SchedEventPost[Status]',
				$model->scenario === 'update' ? $model->Status : 'ACTIVE',
				ZHtml::enumItem($model, 'Status'));
			?>
			<?php echo $form->error($model,'Status'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script>


//dynamic loading
$( document ).ready(function() {

	
	$("#SchedEventPost_AwardType").change(function(){
		hideAwardTypes();	    
	});

	function hideAwardTypes()
	{

	    var choice = $("#SchedEventPost_AwardType" ).val();
	    var cmp    = 0;
	    var brd    = 0;
	    var cnl    = 0;
	    
	    //chk	
	    if(choice.match(/^(POINT|COUPON|REWARD)$/g))
	    {
		    var url    = '';
		    var	$mde   = "BrandId="     + encodeURIComponent(brd) +
		    		 "&CampaignId=" + encodeURIComponent(cmp) +
		    		 "&ChannelId="  + encodeURIComponent(cnl) ;
	    
		    $("#dPointsId").hide();
		    $("#dCouponId").hide();
		    $("#dRewardId").hide();
	    	    if(choice.match(/^(POINT)$/g))
	    	    {
			    $("#dPointsId").show();
	    	    	    url    = BaseUrl + "schedEventPost/getPointlist/?" + $mde;
			    loadlist($('select#SchedEventPost_PointsId').get(0),
				url,
				''
			     );
		    }
	    	    if(choice.match(/^(COUPON)$/g))
	    	    {
			    $("#dCouponId").show();
	    	    	    url    = BaseUrl + "schedEventPost/getCouponlist/?" + $mde;
			    loadlist($('select#SchedEventPost_CouponId').get(0),
				url,
				''
			     );
		    }
	    	    if(choice.match(/^(REWARD)$/g))
	    	    {
			    $("#dRewardId").show();
	    	    	    url    = BaseUrl + "schedEventPost/getRewardlist/?" + $mde;
			    loadlist($('select#SchedEventPost_RewardId').get(0),
				url,
				''
			     );
		    }
 	    }
	    else
	    {
		    $("#dPointsId").hide();
		    $("#dCouponId").hide();
		    $("#dRewardId").hide();
	    }
	}
	
	$("#SchedEventPost_ClientId").change(function(){
	    
	    var url = BaseUrl + "schedEventPost/getcustomers/?ClientId=" + $("#SchedEventPost_ClientId" ).val();;
	    
	});

	$("#SchedEventPost_AwardName").change(function(){
		hideRanges();
	});

	//hide
	function hideRanges()
	{
	    var choice = $("#SchedEventPost_AwardName" ).val();

	    $("#dStartDate").show();
	    $("#dEndDate").show();
	    if(choice.match(/^(BIRTHDATE|ANNIVERSARY|NONE|)$/g))
	    {
		    $("#dStartDate").hide();
		    $("#dEndDate").hide();
	    }
	}

	//add it
	function loadlist(selobj,url,nameattr)
	{
	    $(selobj).empty();
	    $(selobj).append(
		$('<option></option>')
		.val('')
		.html('-- Please Select --'));
	    $.getJSON(url,{},function(data)
	    {
	        $.each(data, function(i,obj)
	        {
	            $(selobj).append(
	                 $('<option></option>')
	                        .val(i)
	                        .html(obj));
	        });
	    });
	}

	//init
	hideAwardTypes();	    
	hideRanges();
});

</script>
