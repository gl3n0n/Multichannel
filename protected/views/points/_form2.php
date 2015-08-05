<?php
/* @var $this PointsController */
/* @var $model Points */
/* @var $form CActiveForm */
?>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
  //'id'=>'points-form',
  'id'=>'Points_Create_Form',
  // Please note: When you enable ajax validation, make sure the corresponding
  // controller action is handling ajax validation correctly.
  // There is a call to performAjaxValidation() commented in generated controller code.
  // See class documentation of CActiveForm for details on this.
  'enableAjaxValidation'=>false,
)); ?>

  <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?/*php if($model->scenario === 'insert'): ?>
    <p class="note">Select a Brand to get the available Campaigns and Channels.<br>You can select multiple Campaigns.<br>You can select multiple Channels.</p>
    <?php endif; */?>

  <?php echo $form->errorSummary($model); ?>

<?/*php if($model->scenario === 'insert'): ?>

    <div class="row">
        <?php echo $form->labelEx($model,'BrandId'); ?>
        <?php echo $form->dropDownList($model,'BrandId',$brand_list, array(
            'prompt'=>'--Select a brand--',
            'ajax'=>array('type'=>'get',
                'url'=>$this->createUrl('/channels/getcampaigns'),
                // 'update'=>'#' . get_class($model) . '_CampaignId',
                'data'=>'js:{"BrandId" : this.value }',
                'success'=>'js:function data(response) {
                    $("#' . get_class($model) . '_CampaignId'.'").html("");
                    $("#' . get_class($model) . '_ChannelId'.'").html("--Select a brand--");

                    var curval = $("#' . get_class($model) . '_BrandId'.'").val();

                    if(response.length === 0)
                    {
                        var promptTextCampaign = ( ! curval) ? "--Select a brand--" : "--Nothing--";
                        $("#' . get_class($model) . '_CampaignId'.'").html($("<option/>", {value: "", text: promptTextCampaign })).attr("size",1);
                        $("#' . get_class($model) . '_ChannelId'.'").html($("<option/>", {value: "", text: promptTextCampaign })).attr("size",1);
                    }
                    else
                    { 
                        $("#' . get_class($model) . '_CampaignId'.'").attr("size", 5);
                        $("#' . get_class($model) . '_ChannelId'.'").html($("<option/>", {value: "", text: "--Select a campaign--" })).attr("size",1);
                    }

                    $.each(response, function(idx, val) {
                        $("#' . get_class($model) . '_CampaignId'.'").append($("<option/>", {
                            value: idx,
                            text: val
                        }));
                    });
                }',
            ),
        )); ?>
        <?php echo $form->error($model,'BrandId'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'CampaignId'); ?>
        <?//php echo $form->dropDownList($model,'CampaignId',$campaign_id); ?>
        <?php echo CHtml::listBox(get_class($model).'[CampaignId]', array(), 
            array('empty'=>'--Select a brand--',
                'ajax'=>array('type'=>'get',
                    'url'=>$this->createUrl('/channels/getchannels'),
                    'data'=>'js:{
                        CampaignId: $.map( $("#' . get_class($model) . '_CampaignId option:selected"), function(e) { return $(e).val() }), 
                        BrandId: $("#' . get_class($model) . '_BrandId").val()
                    }',
                    'success'=>'js: function(response) {
                        console.log(response);
                    }',
                )
            ),
            array('id'=>get_class($model) . '_CampaignId', 'multiple'=>'multiple', 'size'=>1)
        ); ?>
        <?php echo $form->error($model,'CampaignId'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'ChannelId'); ?>
        <?//php echo $form->dropDownList($model,'ChannelId',$campaign_id); ?>
        <?php echo CHtml::listBox(get_class($model).'[ChannelId]', array(), 
            array('empty'=>'--Select a brand--'),
            array('id'=>get_class($model) . '_ChannelId', 'multiple'=>'multiple', 'size'=>1)
        ); ?>
        <?php echo $form->error($model,'CampaignId'); ?>
    </div>

<?php else: */?>
  <div class="row">
    <?php echo $form->labelEx($model,'BrandId'); ?>
    <?php echo $form->dropDownList($model,'BrandId',$brand_list,array(
      'prompt' => '--Select a brand--',
    )); ?>
    <?php echo $form->error($model,'BrandId'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'CampaignId'); ?>
    <?php echo $form->dropDownList($model,'CampaignId',$campaign_list); ?>
    <?php echo $form->error($model,'CampaignId'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'ChannelId'); ?>
    <?php echo $form->dropDownList($model,'ChannelId',$channel_list); ?>
    <?php echo $form->error($model,'ChannelId'); ?>
  </div>
<?/*php endif; */?>
  
  <div class="row">
    <?php echo $form->labelEx($model,'Value'); ?>
    <?php echo $form->textField($model,'Value',array('size'=>11,'maxlength'=>11)); ?>
    <?php echo $form->error($model,'Value'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'PointAction'); ?>
    <?php echo $form->textField($model,'PointAction',array('size'=>11,'maxlength'=>11)); ?>
    <?php echo $form->error($model,'PointAction'); ?>
  </div>
  
  <div class="row"> 
    <?php echo $form->labelEx($model,'PointCapping'); ?>
    <?php echo ZHtml::enumDropDownList(Points::model(), 'PointCapping', array(
    'id'=>'Points_PointCapping',
    'name'=>'Points[PointCapping]',
    'value'=>'',
)); ?>
    <?php echo $form->error($model,'PointCapping'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'From'); ?>
    <?php
      $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'From',
           'id' => 'Points_From',
       'model'=>$model,
      'attribute'=>'From',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#Points_To").datepicker("option", "minDate", selectedDate); }',            
           )
       ));  ?>
    <?php echo $form->error($model,'From'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'To'); ?>
    <?php
       $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'To',
           'id'=>'Points_To',
       'model'=>$model,
      'attribute'=>'To',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#Points_From").datepicker("option", "maxDate", selectedDate); }',
           )
       ));  ?>
    <?php echo $form->error($model,'To'); ?>
  </div>  
  <div class="row">
    <?php echo $form->labelEx($model,'PointsLimit'); ?>
    <?php echo $form->textField($model,'PointsLimit'); ?>
    <?php echo $form->error($model,'PointsLimit '); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'Status'); ?>
    <?php echo ZHtml::enumDropDownList(Points::model(), 'Status', array(
    'id'=>'Points_Status',
    'name'=>'Points[Status]',
    'value'=>'',
)); ?>
    <?php echo $form->error($model,'Status'); ?>
  </div>

  <div class="row buttons">
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
  </div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
  var brandId    = "#Points_BrandId";
  var campaignId = "#Points_CampaignId";
  var channelId  = "#Points_ChannelId";

  $('#Points_BrandId').off("change");
  $('#Points_BrandId').on("change", function(e) {
    
    if(this.value) {

      $.ajax({
        url: BaseUrl + "channels/getcampaigns",
        data: { "BrandId" : this.value },
        type: "GET",
        dataType: "json",
        success: function(response) {
          $(campaignId).html("").append( $("<option/>", { value: "", text: "--Select campaign--" }));
          
          var listLimit = 5;
          
          if(response.size() < listLimit) $(campaignId).attr("size", response.size());
          else $(campaignId).attr("size", listLimit);

          if( response.size() === 0) {
            var promtText = (! this.val) ? "--Select brand--" : "--Nothing--";
            $(campaignId, channelId)
              .html("")
              .append( $("<option/>", { value: "", text: promtText }))
              .attr("size",1);
          } else {
            $(campaignId)
              .html("")
              .append( $("<option/>", { value: "", text: "--Select campaign--" }))
              .attr({"size":5, "multiple": true});
          }

          $.each(response, function(idx, val) {
            $(campaignId).append( $("<option/>", { value: idx, text: val }));
          });
        }
      });

    } else {
      
      $(campaignId).html("")
        .append( $("<option/>", { value: "", text: "--Select brands--" }))
        .attr("size", 1);
      $(channelId).html("")
        .append( $("<option/>", { value: "", text: "--Select brands--" }))
        .attr("size", 1);
    }
    
  });

  $(campaignId).off("change");
  $(campaignId).on("change", function(e) {
    var campaignIds = $(campaignId).val().join(" ");
    var dmData = { 
      "BrandId": this.value, 
      "CampaignId": campaignIds 
    };
    
    $.ajax({
      url: BaseUrl + "channels/getchannels",
      data: dmData,
      type: "GET",
      dataType: "json",
      success: function(response) {
        $(channelId).html("").append( $("<option/>", { value: "", text: "--Select channel--" }));
        
        if( response.length === 0) {
          var promtText = (this.val) ? "--Select brand--" : "--Nothing--";
          $(channelId)
            .html("")
            .append( $("<option/>", { value: "", text: promtText }))
            .attr("size",1);
        } else {
          $(channelId)
            .html("")
            .append( $("<option/>", { value: "", text: "--Select channel--" }))
            .attr({"size":5, "multiple": true});
        }
        
        $.each(response, function(idx, val) {
          $(channelId).append( $("<option/>", { value: idx, text: val }));
        });
      }
    });

  });
</script>
</div><!-- form -->