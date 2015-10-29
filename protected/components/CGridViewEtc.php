<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
Yii::import('zii.widgets.grid.CGridView');
 
class CGridViewEtc extends CGridView {
	public $etc;
    
	public function etcButtonCoupon($data=null,$custlist=null)
	{
		$frmid= $data["GeneratedCouponId"];
		$sfrm = CHtml::beginForm(Yii::app()->createUrl("couponSystem/genapproved"),
				'post',array('id'=>'mainFrm'.$frmid));
		$btn  = CHTML::button('Claim AS',  array(
				'id'     => 'btnClaim' ,
				'style'  => 'width:200px;',
				'onclick'=> "javascript:generateCoupon($frmid);"));
		$efrm = CHtml::endForm(); 
		
		/**
		$txt  = sprintf("%s , %s , %s",
				$data["BrandName"],
				$data["CampaignName"],
				$data["ChannelName"]);
		$str1  = CHtml::textField('BrandName', $data["BrandName"], array(
				     'style' => 'width:200px;','disabled'=>'disabled'));
		$str2  = CHtml::textField('CampaignName', $data["CampaignName"], array(
				     'style' => 'width:200px;','disabled'=>'disabled'));				     
		$str3  = CHtml::textField('ChannelName', $data["ChannelName"], array(
				     'style' => 'width:200px;','disabled'=>'disabled'));	
				     **/
		$sel   = CHtml::dropDownlist('CustomerId','',$custlist, array(
						'id'    => 'CustomerId',
						'style' => 'width:203px;',
						));
		$hid1 = CHtml::hiddenField('CouponId',          $data["CouponId"], array('id'=>'CouponId'));				
		//$hid2 = CHtml::hiddenField('CouponMappingId',   $data["CouponMappingId"], array('id'=>'CouponMappingId'));
		$hid3 = CHtml::hiddenField('GeneratedCouponId', $data["GeneratedCouponId"], array('id'=>'GeneratedCouponId'));				
		$img  = $this->getQrCodeImage($data);
		
		return "
		<div>
		<hr>
		$sfrm
		$hid1
		$hid3
		<table cellpadding=2 cellspacing=2 style='width:100%'>
			<tr>
			<td  align='right' valign='top'>
			   QR-Code
			</td>
			   <td  align='center' valign='top'>
				 $img 
			   </td>
			</tr>
		</table>
		 $efrm
		 </div>
		 ";
		
	}
	
	public function getQrCodeImage($data=null)
	{
		$apiUtils  = new Utils;
		$api   	   = array(
				'data' => array('generated_coupon_id'  => $data["GeneratedCouponId"], 
						'get_qrcode'           => true),
				'url'  => Yii::app()->params['api-url']['get_qrcode'],
				);
				
				
		$ret   = $apiUtils->send2Api($api);
		$src   = Yii::app()->params['api-url']['link_qrcode'];
		$img   = sprintf("%s/coup%s.png",$src , $data["GeneratedCouponId"]);
		
		return  CHtml::image($img,'Coupon Qr Code',array("width"=>"188px" ,"height"=>"188px"));
		//<img src='http://104.156.53.150/multichannel-api/coupon/qr_codes/coup" . $coup["generatedcouponid"] . ".png' />";
		
	}
	
	public function etcButtonRaffle($email='',$custid='')
	{
		$sfrm = CHtml::checkBox('winner[]',"$custid",array('checked'=> true,'value' => "$custid"));
		$str  = CHtml::textField('emailaddress', $email, array(
				     	'style' => 'width:200px;','disabled'=>'disabled'));
		return sprintf("%s &nbsp; %s", $sfrm,$str);		
	}
	
}
