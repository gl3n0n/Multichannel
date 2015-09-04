<?php
/*
$listData
    $listValue
    $listText
$modelName
$attributeName
*/
$className    = $classname;
$elemName     = "{$className}[{$attributeName}][]";
$elemIdPrefix = "{$className}_$attributeName";
$idCheckAll   = "{$elemIdPrefix}_chkAll";
$idCheckNone  = "{$elemIdPrefix}_chkNone";
?>
<div>
  <?php echo CHtml::link('Check All','#', array(
    'id'=>$idCheckAll,
  )); ?>
  <?php echo CHtml::link('Uncheck All','#', array(
    'id'=>$idCheckNone,
  )); ?>
</div>
<div id="<?php echo "{$elemIdPrefix}_Container"; ?>" 
  style="border:1px solid #ccc; 
  width:300px; 
  height: 100px; 
  overflow-y: scroll; 
  margin-bottom: 1em; 
  padding: 0.1em 0.3em;
  ">
  <?php foreach($listData as $listValue => $listText):
    $checked = ($model->{$attributeName} == $listValue) ? 'checked="checked"' : '';
  ?>
  <div>
    <input type="checkbox" name="<?php echo $elemName; ?>" value="<?php echo $listValue; ?>" id="<?php echo "{$elemIdPrefix}_$listValue"; ?>" <?php echo $checked; ?>>
    <label for="<?php echo "{$elemIdPrefix}_$listValue"; ?>" style="display:inline-block;"><?php echo $listText; ?></label>
  </div>
  <?php endforeach; ?>
</div>
<script type="text/javascript">
  var <?php echo $elemIdPrefix; ?> = "<?php echo '#'. $elemIdPrefix .'_Container'; ?>";
  $("#<?=$idCheckAll?>")
    .off()
    .on("click", function(e) {
      e.preventDefault();
      $(<?php echo $elemIdPrefix; ?>).find("[id^=<?php echo $elemIdPrefix; ?>]").prop("checked", true);
      $(<?php echo $elemIdPrefix; ?>).find("[id^=<?php echo $elemIdPrefix; ?>]").prop("checked", true).trigger("change");
      //.trigger("change");
    });

  $("#<?=$idCheckNone?>")
    .off()
    .on("click", function(e) {
      e.preventDefault();
      $(<?php echo $elemIdPrefix; ?>).find("[id^=<?php echo $elemIdPrefix; ?>]").prop("checked", false);
      $(<?php echo $elemIdPrefix; ?>).find("[id^=<?php echo $elemIdPrefix; ?>]").prop("checked", false).trigger("change");
    });
</script>