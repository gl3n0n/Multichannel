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
class AuditLogs extends CActiveRecord
{
	const STATUS_ACTIVE   = 'ACTIVE';
	const STATUS_INACTIVE = 'INACTIVE';
	const STATUS_PENDING  = 'PENDING';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'audit_logs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('AuditId,ClientId,UserId,UserType,UserAgent,IPAddr,GetPost,UrlData,UrlQry,CreatedBy,DateCreated', 'safe', 'on'=>'search'),
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
			'byClients' =>array(self::BELONGS_TO, 'Clients', 'ClientId'),
			'byUsers'   =>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
		);
	}

	public function scopes()
	{
		return array(
			'thisClient'=>array(
				'condition'=>'ClientId = :modelClientId',
				'params' => array(':modelClientId'=>Yii::app()->utils->getUserInfo('ClientId')),
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		//AuditId,ClientId,UserId,UserType,UserAgent,IPAddr,UrlData,UrlQry,CreatedBy,DateCreated,
		return array(
			'AuditId'     => 'Id',
			'ClientId'    => 'Client',
			'UserId'      => 'User Name',
			'UserType'    => 'User Type',
			'UserAgent'   => 'User Agent',
			'IPAddr'      => 'IP',
			'UrlData'     => 'Data',
			'GetPost'     => 'Type',
			'UrlQry'      => 'Query',
			'DateCreated' => 'Date Created',
			'CreatedBy'   => 'Created By',
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
		//AuditId,ClientId,UserId,GetPost,UserType,UserAgent,IPAddr,UrlData,UrlQry,CreatedBy,DateCreated
		$criteria=new CDbCriteria;

		$criteria->compare('AuditId',$this->AuditId,true);
		$criteria->compare('ClientId',$this->ClientId,true);
		$criteria->compare('UserId',$this->UserId,true);
		$criteria->compare('GetPost',$this->GetPost,true);
		$criteria->compare('UserType',$this->UserType,true);
		$criteria->compare('UserAgent',$this->UserAgent,true);
		$criteria->compare('IPAddr',$this->IPAddr,true);
		$criteria->compare('UrlData',$this->UrlData,true);
		$criteria->compare('UrlQry',$this->UrlQry,true);
		
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',$this->CreatedBy);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AuditLogs the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
