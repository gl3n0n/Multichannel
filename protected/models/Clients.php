<?php

/**
 * This is the model class for table "clients".
 *
 * The followings are the available columns in table 'clients':
 * @property string $ClientId
 * @property string $CompanyName
 * @property string $Address
 * @property string $Email
 * @property string $Landline
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class Clients extends CActiveRecord
{
	const STATUS_ACTIVE   = 'ACTIVE';
	const STATUS_INACTIVE = 'INACTIVE';
	const STATUS_PENDING  = 'PENDING';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'clients';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('CompanyName, Address, Landline, Email', 'required'),
			array('CreatedBy, UpdatedBy', 'numerical', 'integerOnly'=>true),
			array('CompanyName', 'length', 'max'=>32),
			array('Address', 'length', 'max'=>255),
			array('Email', 'length', 'max'=>50),
			array('Landline', 'length', 'max'=>30),
			array('Status', 'length', 'max'=>8),
			array('Status', 'in', 'range'=>array(
				self::STATUS_ACTIVE, 
				self::STATUS_INACTIVE, 
				self::STATUS_PENDING
			)),
			array('CreatedBy', 'required', 'on'=>'insert'),
			array('UpdatedBy', 'required', 'on'=>'update'),
			array('DateCreated, DateUpdated', 'safe'),
			// Call them custom validations. Bale param1 = column name, param2 = function name na naka declare sa baba
            array('Email', 'email'),
            array('Landline', 'validateContactNumber'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ClientId, CompanyName, Address, Email, Landline, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy', 'safe', 'on'=>'search'),
		);
	}
	
	// Custom validation ng ContactNumber
    public function validateContactNumber()
    {
        if(! ctype_digit($this->Landline))
            $this->addError('Landline', 'Invalid landline.');
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'userInfo'=>array(self::HAS_ONE, 'Users', 'ClientId'),
			'clientCreateUsers'=>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
			'clientUpdateUsers'=>array(self::BELONGS_TO, 'Users', 'UpdatedBy'),
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
			'ClientId' => 'Client',
			'CompanyName' => 'Company Name',
			'Address' => 'Address',
			'Email' => 'Email',
			'Landline' => 'Landline',
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

		$criteria->compare('ClientId',$this->ClientId,true);
		$criteria->compare('CompanyName',$this->CompanyName,true);
		$criteria->compare('Address',$this->Address,true);
		$criteria->compare('Email',$this->Email,true);
		$criteria->compare('Landline',$this->Landline,true);
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
	 * @return Clients the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
