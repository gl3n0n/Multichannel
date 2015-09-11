<?php

/**
 * This is the model class for table "coupon".
 *
 * The followings are the available columns in table 'coupon':
 * @property string $CouponSystemId
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
class CouponSystem extends CActiveRecord
{
	public $Coupon;
	public $CouponMode;
	public $ClientId;
	
	public $Types = array(
		'ALPHA-NUMERIC' => 'ALPHA-NUMERIC',  
		'ALPHA'         => 'ALPHA',          
		'NUMERIC'       => 'NUMERIC',
	);
	
	public $CouponTypes = array(
		'REGULAR'          => 'REGULAR',          
		'CONVERT_TO_POINTS'=> 'CONVERT_TO_POINTS',
	);
	
	public $Statuses = array(
			'PENDING' => 'PENDING',          
			'ACTIVE'  => 'ACTIVE',
			'INACTIVE'=> 'INACTIVE',
	);
	
	
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

		//
		//CouponId,ClientId,PointsId,Code,CouponName,Type,TypeId,Source,
		//ExpiryDate, CodeLength, CouponType,PointsValue,Status,Image,Quantity,
		//LimitPerUser,File,ImagePath,edit_flag,
		//DateCreated,CreatedBy,DateUpdated,UpdatedBy,
		//

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('CouponName,PointsId,ClientId,Type,LimitPerUser,ExpiryDate', 'required'),
			array('File',       'checkCouponMode'),
			array('CouponMode', 'checkPointsValue'),
			array('ClientId,PointsId,CodeLength,PointsValue, Quantity, LimitPerUser, CreatedBy, UpdatedBy', 'numerical', 'integerOnly'=>true),
			array('Type, TypeId, Source', 'length', 'max'=>50),
			array('Status', 'length', 'max'=>8),
			array('Quantity, LimitPerUser', 'length', 'max'=>11),
			array('Image, File, ImagePath', 'length', 'max'=>200),
			array('File', 'file', 'types'=>'csv', 'safe'=>true, 'allowEmpty'=>true),
			array('Image','file', 'types'=>'gif, png, jpg, jpeg', 'safe'=>true, 'allowEmpty'=>true, 'maxSize'=>5242880,
				"tooLarge"   =>"Please choose a file with size up to 5MB",
				"wrongType"  =>"Your photo must be a jpg,gif or png .",
				"allowEmpty" => true,),
			array('DateCreated, DateUpdated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('CouponId,ClientId,PointsId,Code,CouponName,Type,TypeId,Source,ExpiryDate, CodeLength, CouponType,PointsValue,Status,Image,Quantity,LimitPerUser,File,ImagePath,edit_flag,DateCreated,CreatedBy,DateUpdated,UpdatedBy', 'safe', 'on'=>'search'),
		);
	}
	
	public function scopes()
	{
		return array(
			'thisClient'=>array(
				'condition'=>'ClientId = :modelClientId',
				'params' => array(':modelClientId'=>Yii::app()->utils->getUserInfo('ClientId')),
			),
			'active'=>array(
				'condition'=>"Status='ACTIVE'",
			),
		);
	}

	public function checkCouponMode($attributes, $params)
	{
		if($this->CouponMode == 'user') {
			if(empty($this->File)) $this->addError('File', 'File cannot be blank.');
		} else {
			if(($this->Source==='')   or ($this->Source===null)) 
				$this->addError('Source', 'Source cannot be blank.');
			if(($this->Quantity==='') or ($this->Quantity===null)) 
				$this->addError('Quantity', 'Quantity cannot be blank.');
			if(empty($this->CodeLength) && $this->CodeLength == 0) 
				$this->addError('CodeLength', 'Code length error.');
		}

	}

	public function checkPointsValue()
	{
		if($this->CouponMode == 'user'){
			if($this->CouponType == 'CONVERT_TO_POINTS') {
				if(@intval($this->PointsValue) <= 0) 
					$this->addError('PointsValue', 'PointsValue must be Numeric.');
			}
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
            		'couponCreateUsers'=>array(self::BELONGS_TO, 'Users',        'CreatedBy'),
			'couponUpdateUsers'=>array(self::BELONGS_TO, 'Users',        'UpdatedBy'),
			'couponClients'    =>array(self::MANY_MANY,  'Clients', 
                				'coupon_mapping(CouponMappingId,ClientId)',
                				'through' => 'couponMap'),
			'byClients'        =>array(self::BELONGS_TO, 'Clients',       'ClientId'),                				
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		//
		//CouponId,ClientId,PointsId,Code,CouponName,Type,TypeId,Source,
		//ExpiryDate, CodeLength, CouponType,PointsValue,Status,Image,Quantity,
		//LimitPerUser,File,ImagePath,edit_flag,
		//DateCreated,CreatedBy,DateUpdated,UpdatedBy,
		//
		return array(
			'CouponId'   => 'ID',
			'ClientId'   => 'Client',
			'PointsId'   => 'Points System',
			'Code'       => 'Code',
			'CouponName' => 'Name',
			'Type'       => 'Type',
			'TypeId'     => 'Type Id',
			'Source'     => 'Source',
			'ExpiryDate' => 'Expiry Date',
			'CodeLength' => 'Code Length',
			'CouponType' => 'Coupon Type',
			'PointsValue'=> 'Points',
			'Status'     => 'Status',
			'Image'        => 'Image',
			'Quantity'     => 'Quantity',
			'LimitPerUser' => 'Limit Per User',
			'File'         => 'File',
			'ImagePath'    => 'Image Path',
			'edit_flag'    => 'Edit Flag',
			'DateCreated'  => 'Date Created',
			'CreatedBy'    => 'Created By',
			'DateUpdated'  => 'Date Updated',
			'UpdatedBy'    => 'Updated By',
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



		//
		//CouponId,ClientId,PointsId,Code,CouponName,Type,TypeId,Source,
		//ExpiryDate, CodeLength, CouponType,PointsValue,Status,Image,Quantity,
		//LimitPerUser,File,ImagePath,edit_flag,
		//DateCreated,CreatedBy,DateUpdated,UpdatedBy,
		//


		$criteria->compare('CouponId',$this->CouponId,true);
		$criteria->compare('ClientId',$this->ClientId,true);
		$criteria->compare('PointsId',$this->PointsId,true);
		$criteria->compare('Code',$this->Code,true);
		$criteria->compare('CouponName',$this->CouponName,true);
		$criteria->compare('Type',$this->Type,true);
		$criteria->compare('TypeId',$this->TypeId,true);
		$criteria->compare('Source',$this->Source,true);
		
		$criteria->compare('ExpiryDate',$this->ExpiryDate,true);
		$criteria->compare('CodeLength',$this->CodeLength,true);
		$criteria->compare('CouponType',$this->CouponType,true);
		$criteria->compare('PointsValue',$this->PointsValue,true);
		$criteria->compare('Status',$this->Status,true);
		$criteria->compare('Image',$this->Image,true);
		$criteria->compare('Quantity',$this->Quantity,true);
		
		$criteria->compare('LimitPerUser',$this->LimitPerUser,true);
		$criteria->compare('File',$this->File,true);
		$criteria->compare('ImagePath',$this->ImagePath,true);
		$criteria->compare('edit_flag',$this->edit_flag,true);

		
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',$this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',$this->UpdatedBy);

		
		

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CouponSystem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
