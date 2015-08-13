<?php

/**
 * This is the model class for table "coupon".
 *
 * The followings are the available columns in table 'coupon':
 * @property string $CouponId
 * @property string $Code
 * @property string $Type
 * @property string $TypeId
 * @property string $Source
 * @property string $ExpiryDate
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CodeLength
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 * @property string $Image
 * @property string $Quantity
 * @property string $LimitPerUser
 * @property string $File
 * @property string $ImagePath
 * @property string $edit_flag
 */
class Coupon extends CActiveRecord
{
	public $CouponMode;
	public $ClientId;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'coupon';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Type, LimitPerUser', 'required'),
			// array('ClientId', 'required', 'on'=>'insert'),
			array('File', 'checkCouponMode'),
			array('CodeLength, CreatedBy, UpdatedBy', 'numerical', 'integerOnly'=>true),
			array('Type, TypeId, Source', 'length', 'max'=>50),
			array('Status', 'length', 'max'=>8),
			array('Quantity, LimitPerUser', 'length', 'max'=>11),
			array('Image, File, ImagePath', 'length', 'max'=>200),
			array('File', 'file', 'types'=>'csv', 'safe'=>true, 'allowEmpty'=>true),
			array('Image', 'file', 'types'=>'gif, png, jpg, jpeg', 'safe'=>true, 'allowEmpty'=>true, 'maxSize'=>5242880,
                "tooLarge"=>"Please choose a file with size up to 5MB",
                "wrongType"=>"Your photo must be a jpg,gif or png .",
                "allowEmpty" => true,),
			array('CouponMode, ExpiryDate, DateCreated, DateUpdated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('CouponId, Type, TypeId, Source, ExpiryDate, Status, DateCreated, CodeLength, CreatedBy, DateUpdated, UpdatedBy, Image, Quantity, LimitPerUser, ImagePath', 'safe', 'on'=>'search'),
		);
	}

	public function checkCouponMode($attributes, $params)
	{
		if($this->CouponMode=='user') {
			if(empty($this->File)) $this->addError('File', 'File cannot be blank. XD');
		} else {
			if(($this->Source==='') or ($this->Source===null)) $this->addError('Source', 'Source cannot be blank.');
			if(($this->Quantity==='') or ($this->Quantity===null)) $this->addError('Quantity', 'Quantity cannot be blank.');
			if(empty($this->CodeLength) && $this->CodeLength == 0) $this->addError('CodeLength', 'Code length error.');
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            		'couponMap'        =>array(self::HAS_MANY,   'CouponMapping','CouponId'),
            		'couponCreateUsers'=>array(self::BELONGS_TO, 'Users',  'CreatedBy'),
			'couponUpdateUsers'=>array(self::BELONGS_TO, 'Users',  'UpdatedBy'),
			/**
			'couponClients'    =>array(self::MANY_MANY,  'Clients', 
                				'Coupon_Mapping(CouponMappingId,ClientId)'),
			'couponBrands'     =>array(self::MANY_MANY,  'Brands', 
                				'Coupon_Mapping(CouponMappingId,BrandId)'), 
			'couponChannels'   =>array(self::MANY_MANY,  'Channels', 
                				'Coupon_Mapping(CouponMappingId,ChannelId)'),                				                				
			'couponCampaigns'  =>array(self::MANY_MANY,  'Campaigns', 
                				'Coupon_Mapping(CouponMappingId,CampaignId)'),                				                				
                				**/
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'CouponId' => 'Coupon',
			'Code' => 'Code',
			'Type' => 'Type',
			'TypeId' => 'Type',
			'Source' => 'Source',
			'ExpiryDate' => 'Expiry Date',
			'Status' => 'Status',
			'DateCreated' => 'Date Created',
			'CreatedBy' => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy' => 'Updated By',
			'Image' => 'Image',
			'Quantity' => 'Quantity',
			'LimitPerUser' => 'Limit Per User',
			'File' => 'File',
			'CodeLength' => 'CodeLength',
			'ImagePath' => 'Image Path',
			'CouponMode' => 'Select Coupon Mode',
			'ClientId' => 'Client',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('CouponId',$this->CouponId,true);
		$criteria->compare('Code',$this->Code,true);
		$criteria->compare('Type',$this->Type,true);
		$criteria->compare('TypeId',$this->TypeId,true);
		$criteria->compare('Source',$this->Source,true);
		$criteria->compare('ExpiryDate',$this->ExpiryDate,true);
		$criteria->compare('Status',$this->Status,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CodeLength',$this->CodeLength);
		$criteria->compare('CreatedBy',$this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',$this->UpdatedBy);
		$criteria->compare('BrandId',$this->BrandId,true);
		$criteria->compare('CampaignId',$this->CampaignId,true);
		$criteria->compare('ChannelId',$this->ChannelId,true);
		$criteria->compare('Image',$this->Image,true);
		$criteria->compare('Quantity',$this->Quantity,true);
		$criteria->compare('LimitPerUser',$this->LimitPerUser,true);
		$criteria->compare('File',$this->File,true);
		$criteria->compare('ImagePath',$this->ImagePath,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Coupon the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
