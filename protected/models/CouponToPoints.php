<?php

/**
 * This is the model class for table "points".
 *
 * The followings are the available columns in table 'points':
 * @property string $CouponToPointsId
 * @property string $ClientId
 * @property string $CouponId
 * @property string $StartDate
 * @property string $EndDate
 * @property string $Name
 * @property string $Value
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class CouponToPoints extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'coupon_to_points';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ClientId,CouponId,Name,Value', 'required'),
			array('ClientId,CouponId,Value', 'numerical', 'integerOnly'=>true),
			array('Status', 'length', 'max'=>8),
			array('Value',  'match', 'pattern' =>'/^[0-9]+$/'),
			array('Value',  'moreThanZero'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('CtpId,ClientId,CouponId,Value,Status,StartDate,EndDate,DateCreated, CreatedBy, DateUpdated, UpdatedBy', 'safe', 'on'=>'search'),
		);
	}

	public function moreThanZero()
	{
		if(@intval($this->Value) <= 0) 
		    $this->addError('Value', 'PointsValue must be Numeric.');
		
	}
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'byCoupon'     =>array(self::BELONGS_TO, 'Coupon', 'CouponId'),
			'byClients'    =>array(self::BELONGS_TO, 'Clients','ClientId'),
			'byCreateUsers'=>array(self::BELONGS_TO, 'Users',  'CreatedBy'),
			'byUpdateUsers'=>array(self::BELONGS_TO, 'Users',  'UpdatedBy'),
			'byPoints'     =>array(self::MANY_MANY,  'PointsSystem',
													 'coupon(CouponId,PointsId)',
													 'through' => 'byCoupon'),
			
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
			'CtpId'       => 'Id',
			'ClientId'    => 'Client',
			'CouponId'    => 'Coupon Id',
			'Value'       => 'Coupon Value',
			'Name'        => 'Coupon Name',
			'StartDate'   => 'Start Date',
			'EndDate'     => 'End Date',
			'Status'      => 'Status',
			'DateCreated' => 'Date Created',
			'CreatedBy'   => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy'   => 'Updated By',
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

		$criteria->compare('CtpId',      $this->CtpId,true);
		$criteria->compare('CouponId',   $this->CouponId,true);
		$criteria->compare('ClientId',   $this->ClientId,true);
		$criteria->compare('Value',      $this->Value,true);
		$criteria->compare('Name',       $this->Name,true);
		$criteria->compare('StartDate',  $this->StartDate,true);
		$criteria->compare('EndDate',    $this->EndDate,true);
		$criteria->compare('Status',     $this->Status,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',  $this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',  $this->UpdatedBy);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CouponToPoints the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
