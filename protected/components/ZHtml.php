<?php
/**
 * Reference: http://www.yiiframework.com/wiki/303/drop-down-list-with-enum-values-for-column-of-type-enum-incorporate-into-giix/
 *
 */
/** :TODO: add filter to the values returned */
class ZHtml extends CHtml
{
    public static function enumDropDownList($model, $attribute, $htmlOptions=array(), $default=NULL)
    {
        $enumValues = self::enumItem($model, $attribute);

        if (!empty($default))
            // This will return a <option value="defaul"></option>
            $enumValues = array($default['value']=>(string)$default['label']) + $enumValues;

        // echo var_export($enumValues, TRUE);

        return CHtml::activeDropDownList( $model, $attribute, $enumValues, $htmlOptions);
    }
 
    public static function enumItem($model, $attribute) {
        $attr=$attribute;
        self::resolveName($model,$attr);
        preg_match('/\((.*)\)/',$model->tableSchema->columns[$attr]->dbType,$matches);
        foreach(explode(',', $matches[1]) as $value) {
                $value=str_replace("'",null,$value);
                $values[$value]=Yii::t('enumItem',$value);
        }
        return $values;
    }
}
?>