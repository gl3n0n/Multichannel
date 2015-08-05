<?php

/**
 * This is the model class for table "generated_coupons".
 *
 * The followings are the available columns in table 'generated_coupons':
 * @property string $GeneratedCouponId
 * @property string $CouponId
 * @property integer $CustomerId
 * @property string $Status
 * @property string $Code
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 * @property string $DateRedeemed
 * @property string $CouponMappingId
 */
class GeneratedCoupons extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'generated_coupons';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('CouponId, CouponMappingId', 'required'),
			array('CustomerId, CreatedBy, UpdatedBy', 'numerical', 'integerOnly'=>true),
			array('CouponId, CouponMappingId', 'length', 'max'=>11),
			array('Status', 'length', 'max'=>8),
			array('Code', 'length', 'max'=>200),
			array('DateCreated, DateUpdated, DateRedeemed', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('GeneratedCouponId, CouponId, CustomerId, Status, Code, DateCreated, CreatedBy, DateUpdated, UpdatedBy, DateRedeemed, CouponMappingId', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'mappingdetails'=>array(self::HAS_ONE, 'CouponMapping', 'CouponMappingId'),
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'GeneratedCouponId' => 'Generated Coupon',
			'CouponId' => 'Coupon',
			'CustomerId' => 'Customer',
			'Status' => 'Status',
			'Code' => 'Code',
			'DateCreated' => 'Date Created',
			'CreatedBy' => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy' => 'Updated By',
			'DateRedeemed' => 'Date Redeemed',
			'CouponMappingId' => 'Coupon Mapping',
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

		$criteria->compare('GeneratedCouponId',$this->GeneratedCouponId,true);
		$criteria->compare('CouponId',$this->CouponId,true);
		$criteria->compare('CustomerId',$this->CustomerId);
		$criteria->compare('Status',$this->Status,true);
		$criteria->compare('Code',$this->Code,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',$this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',$this->UpdatedBy);
		$criteria->compare('DateRedeemed',$this->DateRedeemed,true);
		$criteria->compare('CouponMappingId',$this->CouponMappingId,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GeneratedCoupons the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
