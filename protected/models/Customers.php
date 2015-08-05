<?php

/**
 * This is the model class for table "customers".
 *
 * The followings are the available columns in table 'customers':
 * @property string $CustomerId
 * @property string $FirstName
 * @property string $MiddleName
 * @property string $LastName
 * @property string $Gender
 * @property string $ContactNumber
 * @property string $Address
 * @property string $Email
 * @property string $FBId
 * @property string $TwitterHandle
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class Customers extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'customers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('CreatedBy, UpdatedBy', 'numerical', 'integerOnly'=>true),
			array('FirstName, MiddleName, LastName', 'length', 'max'=>50),
			array('Gender', 'length', 'max'=>1),
			array('ContactNumber, Address', 'length', 'max'=>255),
			array('Email, FBId, TwitterHandle', 'length', 'max'=>30),
			array('Status', 'length', 'max'=>8),
			array('DateCreated, DateUpdated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('CustomerId, FirstName, MiddleName, LastName, Gender, ContactNumber, Address, Email, FBId, TwitterHandle, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'CustomerId' => 'Customer',
			'FirstName' => 'First Name',
			'MiddleName' => 'Middle Name',
			'LastName' => 'Last Name',
			'Gender' => 'Gender',
			'ContactNumber' => 'Contact Number',
			'Address' => 'Address',
			'Email' => 'Email',
			'FBId' => 'Fbid',
			'TwitterHandle' => 'Twitter Handle',
			'Status' => 'Status',
			'DateCreated' => 'Date Created',
			'CreatedBy' => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy' => 'Updated By',
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

		$criteria->compare('CustomerId',$this->CustomerId,true);
		$criteria->compare('FirstName',$this->FirstName,true);
		$criteria->compare('MiddleName',$this->MiddleName,true);
		$criteria->compare('LastName',$this->LastName,true);
		$criteria->compare('Gender',$this->Gender,true);
		$criteria->compare('ContactNumber',$this->ContactNumber,true);
		$criteria->compare('Address',$this->Address,true);
		$criteria->compare('Email',$this->Email,true);
		$criteria->compare('FBId',$this->FBId,true);
		$criteria->compare('TwitterHandle',$this->TwitterHandle,true);
		$criteria->compare('Status',$this->Status,true);
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
	 * @return Customers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
