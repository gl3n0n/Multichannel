<?php
/* @var $this PointsController */
/* @var $model Points */
/* @var $form CActiveForm */

//echo '<pre>';
//print_r($channels_list);
if(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert') {
    $clients_model = Clients::model()->active()->findAll();
    $client_list = CHtml::listData($clients_model, 'ClientId', 'CompanyName');
}
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

  <?php
      foreach(Yii::app()->user->getFlashes() as $key => $message) {
          echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
      }
  ?>

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
    <?php if(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert'): ?>
    <div class="row">
        <?php echo $form->labelEx($model,'ClientId'); ?>
        <?php echo $form->dropDownList($model,'ClientId',$client_list, array(
            'empty'=>'--Select Client--',
            'ajax'=>array('type'=>'get',
                'url'=>$this->createUrl('/channels/getbrands'),
                // 'update'=>'#' . get_class($model) . '_CampaignId',
                'data'=>'js:{"ClientId" : this.value }',
                'success'=>'js:function data(response) {
                    $("#' . get_class($model) . '_BrandId'.'").html("");
                    $("#' . get_class($model) . '_CampaignId'.'").html($("<option/>", {value: "", text: "--Select a client--" })).attr("size", 1);

                    var curval = $("#' . get_class($model) . '_ClientId'.'").val();

                    if(response.length === 0 || ! curval)
                    {
                        var promptText = "--Select a client--";
                    }
                    else {
                        var promptText = "--Select a brand--";
                        $("#' . get_class($model) . '_CampaignId'.'")
                          .html($("<option/>", {value: "", text: "--Select a brand--" }))
                          .attr("size", 1);
                    }

                    $("#' . get_class($model) . '_BrandId'.'").html($("<option/>", {value: "", text: promptText }));

                    $.each(response, function(idx, val) {
                        $("#' . get_class($model) . '_BrandId'.'").append($("<option/>", {
                            value: idx,
                            text: val
                        }));
                    });
                }',
            ),
        )); ?>
        <?php echo $form->error($model,'ClientId'); ?>
    </div>
    <?php endif; ?>

<?php if($model->scenario === 'insert'): // These are displayed when user is creating a new coupon. ?>

  <div class="row">
    <?php echo $form->labelEx($model,'BrandId'); ?>
    <?php echo $form->dropDownList($model,'BrandId',$brands_list,array(
      'prompt' => '--Select a brand--',
    )); ?>
    <?php echo $form->error($model,'BrandId'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'CampaignId'); ?>
    <?php $this->renderPartial('application.components.view.checkboxlist', array(
      'model'=>$model,
      'listData'=>$campaigns_list,
      'attributeName'=>'CampaignId',
    )); ?>
    <?php echo $form->error($model,'CampaignId'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'ChannelId'); ?>
    <?php $this->renderPartial('application.components.view.checkboxlist', array(
      'model'=>$model,
      'listData'=>$channels_list,
      'attributeName'=>'ChannelId',
    )); ?>
    <?php echo $form->error($model,'ChannelId'); ?>
  </div>

<?php else: // End Create scenario ?>

	<div class="row">
        <?php echo $form->labelEx($model,'ClientId'); ?>
        <?php echo $model->pointClients->CompanyName; ?>
        <?php echo $form->error($model,'ClientId'); ?>
    </div>
	
	<div class="row">
        <?php echo $form->labelEx($model,'BrandId'); ?>
        <?php echo $model->pointBrands->BrandName; ?>
        <?php echo $form->error($model,'BrandId'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'CampaignId'); ?>
		<?php echo $model->pointCampaigns->CampaignName; ?>
		<?php echo $form->error($model,'CampaignId'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'ChannelId'); ?>
		<?php echo $model->pointChannels->ChannelName; ?>
		<?php echo $form->error($model,'ChannelId'); ?>
	</div>

<?php endif; // End Create scenario ?>
  
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
           'name' => 'Points[From]',
           'id' => 'Points_From',
     //  'model'=>$model,
	   'value'=>substr($model->From,0,10),
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
               'onClose' => 'js:function(selectedDate) { jQuery("#Points_To").datepicker("option", "minDate", selectedDate); }',            
           )
       ));  ?>
    <?php echo $form->error($model,'From'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'To'); ?>
    <?php
       $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'Points[To]',
           'id'=>'Points_To',
 //      'model'=>$model,
           'value'=>substr($model->To,0,10),
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
               'onClose' => 'js:function(selectedDate) { jQuery("#Points_From").datepicker("option", "maxDate", selectedDate); }',
           )
       ));  ?>
    <?php echo $form->error($model,'To'); ?>
  </div>  
  <div class="row">
    <?php echo $form->labelEx($model,'PointsLimit (Put 0 if Unlimited)'); ?>
    <?php echo $form->textField($model,'PointsLimit'); ?>
    <?php echo $form->error($model,'PointsLimit'); ?>
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

var CampaignList = function()
{
  var self = this;

  self.listData = {};
  self.brandId = "#Points_BrandId";
  self.campaignContainerId = "#Points_CampaignId_Container";
  self.channelContainerId = "#Points_ChannelId_Container";
  self.chkCampaignItems = '#Points_CampaignId_Container input[id^=Points_CampaignId_]';
  self.chkAllCampaignId

  self.init = function()
  {
    jQuery(self.channelContainerId).empty();
    jQuery(self.campaignContainerId).empty();
    self.addEvents();
  };

  self.addEvents = function()
  {
    jQuery(self.brandId)
      .off()
      .on("change", function() {
        self.getData(this.value, [
          self.render,
          self.addEvents
        ]);
      }); // End on

    jQuery(self.chkCampaignItems)
      .off()
      .on("change", function() {
        self.getChannelList(jQuery(self.brandId).val());
    });

    jQuery()
      .off()
      .on("change", function() {
        self.getChannelList(jQuery(self.brandId).val());
    });
  };

  self.getListData = function(BrandId, callbacks)
  {
    self.getData(BrandId);
  };

  self.getChannelList = function() {
      var requestObject = {
          url: BaseUrl + "channels/getchannels",
          type: "GET",
          dataType: "json"
      };

      requestObject.data = { 
          BrandId: jQuery(self.brandId).val(),
          CampaignId: self.getCampaignIds()
      };

      requestObject.beforeSend = function() {
          jQuery(self.channelContainerId).html("Loading...");
      };

      requestObject.success = function(response) {
          self.refreshChannels(response);
      };

      jQuery.ajax(requestObject);
  };

  self.getData = function(BrandId, callbacks)
  {
    rData = {"BrandId": BrandId};

    // Send request
    jQuery.ajax({
      url: BaseUrl + "channels/getcampaigns",
      data: rData,
      type: 'GET',
      dataType: 'json',
      beforeSend: function() {
        jQuery(self.campaignContainerId).html("Loading...");
      },
      success: function(response) {
        // Empty the contents of list
        jQuery(self.campaignContainerId).empty();

        if( response ) self.listData = response;

        if(callbacks) {
          jQuery.each(callbacks, function(idx, callback) {
            callback();
          });
        }

      }
    }); // End request

  };

  self.getCampaignIds = function() {
      return jQuery(self.chkCampaignItems + ":checked").map(function() { return this.value }).get();
  };

  self.refreshChannels = function(data)
  {
    jQuery(self.channelContainerId).empty();

    jQuery.each(data, function(idx, val) {
      var itemContainer = jQuery("<div/>");

      // Create the item
      itemContainer
        .append( // make checkbox
          jQuery("<input>", { type: "checkbox", id: "Points_ChannelId_" + idx,
            value: idx, name: "Points[ChannelId][]" })
        )
        .append("&nbsp;")
        .append( // make label
          jQuery("<label/>", { "for": "Points_ChannelId_" + idx, text: val })
            .css("display", "inline-block")
        )
      // 
      jQuery(self.channelContainerId).append(itemContainer);
    });
  };

  self.render = function()
  {
    jQuery.each(self.listData, function(idx, val) {
      var itemContainer = jQuery("<div/>");

      // Create the item
      itemContainer
        .append( // make checkbox
          jQuery("<input>", { type: "checkbox", id: "Points_CampaignId_" + idx,
            value: idx, name: "Points[CampaignId][]" })
        )
        .append("&nbsp;")
        .append( // make label
          jQuery("<label/>", { "for": "Points_CampaignId_" + idx, text: val })
            .css("display", "inline-block")
        )
      // 
      jQuery(self.campaignContainerId).append(itemContainer);
    });
  };
};

var campaignsList = new CampaignList();
campaignsList.init();



// EVENT: Brands dropdown list

// END EVENT: Brands dropdown list
</script>

</div><!-- form -->
