<?php

class UsersController extends Controller
{
	public $extraJS;
	public $extraCSS;
	public $mainDivClass;
	public $modals;

	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'accessControl',
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@')
				),
			array('deny'),

		);
	}

	private function sendVerificationEmail($userid, $email)
	{
		$UserModel = Users::model()->findByPk($userid);
		$content = $this->renderPartial('email/verifyUser', array('username'=>$UserModel->username), true);

		$subject = 'Multichannel - Verify Your Account';

		$from_email = Yii::app()->params['adminEmail'];
		$sender = 'Accounts Admin';

		$name='=?UTF-8?B?'.base64_encode($sender).'?=';
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
		$headers="From: $sender <{$from_email}>\r\n".
			"Reply-To: {$from_email}\r\n".
			"MIME-Version: 1.0\r\n".
			"Content-Type: text/plain; charset=UTF-8";

		mail($email,$subject,$content,$headers);
	}

	private function sendWelcomeEmail($userid)
	{
		$UserModel = Users::model()->findByPk($userid);
		$content = $this->renderPartial('email/welcomeUser', array('username'=>$UserModel->username), true);

		$subject = 'Multichannel - Account Verified';

		$from_email = Yii::app()->params['adminEmail'];
		$sender = 'Accounts Admin';

		$name='=?UTF-8?B?'.base64_encode($sender).'?=';
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
		$headers="From: $sender <{$from_email}>\r\n".
			"Reply-To: {$from_email}\r\n".
			"MIME-Version: 1.0\r\n".
			"Content-Type: text/plain; charset=UTF-8";

		mail($email,$subject,$content,$headers);
	}

	private function createValidationLink($userid)
	{
		// The return should be like this:
		// http://multichannel/users/activate?id=#&vcode=hereherehere
		// 
		$UserModel = Users::model()->findByPk($userid);
		$code = sha1($UserModel->UserId . microtime() . $UserModel->username);
		return $code;
	}

	public function actionActivate()
	{
		// There are two routines:
		// 1. User clicks on the link sent with the email
		// 2. User manually enters his verification code.

		if($_GET['vcode'])
		{
			// This is routine 1.
		}
		else
		{
			// This is routine 2.
			// User will be asked his username, email and verification code.
			$FormModel = new FormActivate;
			$this->render('activate');
		}
	}

	// Validation rules? Look in the model classes.
	public function actionDoCreate()
	{
		if(isset($_POST['Users'], $_POST['Clients']))
		{
			$response = array();
			$transaction = Yii::app()->db->beginTransaction();
			$UserSaved = false;
			$ValidUser = false;
			$ClientSaved = true;

			try {
				$UsersModel = new Users;
				$UsersModel->attributes = $_POST['Users'];
				$UsersModel->DateCreated = new CDbExpression('NOW()');
				$UsersModel->DateUpdated = new CDbExpression('NOW()');
				$UsersModel->CreatedBy = Yii::app()->user->id;
				
				if($UsersModel->AccessType === 'SUPERADMIN') 
				{
					$ClientSaved = true; // Bypass the client creation
					$UsersModel->ClientId = 0;
				}
				else
				{
					$ClientSaved = true; // Bypass the client creation
					$ClientsModel = new Clients;
					$ClientsModel->attributes = $_POST['Clients'];
					$UsersModel->ClientId = $ClientsModel->ClientId;
				}

				$UserValidated = $UsersModel->validate();
				
				// chk-uniq-user
				$userNameMod = $UsersModel->userNameExistsNew($UsersModel->Username);
				if( $userNameMod )
				{
					$UsersModel->addError('Username', 'User already exists. ');
					$UserValidated = 0;
				}
				//chk uniq-email
				$emailModel = $UsersModel->emailExistsNew($UsersModel->Email);
				if( $emailModel )
				{
					$UsersModel->addError('Email', 'Email already exists. ');
					$UserValidated = 0;
				}
				//chk client-id
				if($UsersModel->AccessType === 'ADMIN') 
				{
					$clientId = trim($_POST['Clients']['ClientId']);
					$ClientsModel->ClientId = $clientId;
					$UsersModel->ClientId   = $clientId;
					
					if($clientId == '' or $clientId == 0)
					{
						$ClientsModel->addError('ClientId', 'Must select a valid Client. ');
						$UserValidated = 0;
					}
				}
				// echo "<hr> validate: $UserValidated<hr>" . @var_export($UsersModel,true);
				// exit;
				// Encryption is done in the model, beforeSave()
        		        //$UsersModel->Password = md5($_POST['Users']['Password']);

				if($UserValidated && $ClientSaved)
				{
					$UsersModel->save(false);
					$response = array(
						'message'=>'User successfully created.',
					);
					$utilLog = new Utils;
					$utilLog->saveAuditLogs();

				}
				else
				{
					if($UsersModel->AccessType === 'SUPERADMIN') 
					{
						$fieldErrors = array(
							'Users' => $UsersModel->errors
						);
					}
					else
					{
						$fieldErrors = array(
							'Clients' => $ClientsModel->errors,
							//'Users' => $UsersModel->errors
							'Users' => $UsersModel->errors
						);
					}
					

					$response = array(
						'error'=>true,
						'message'=>'User not saved.',
						'fieldErrors'=>$fieldErrors,
					);
				}
				
				$transaction->commit();
				Yii::app()->utils->sendJSONResponse($response);

			} catch (CDbException $ex) {
				$transaction->rollback();
				Yii::app()->utils->sendJSONResponse(array(
					'hasError'=>true,
					'message'=>'We have encountered an error while saving your data.',
					'data'=>$ex->getMessage(),				));
			} catch (Exception $ex) {
				$transaction->rollback();
				Yii::app()->utils->sendJSONResponse(array(
					'hasError'=>true,
					'message'=>'Oops! Something went wrong. Please try again in a few minutes.',
					'data'=>$ex->getMessage(),
				));
			}
		}
		else
		{
			Yii::app()->utils->sendJSONResponse(array(
				'hasError'=>true,
				'message'=>'Invalid action',
			));
		}
	}

	// Validation rules? Look in the model classes.
	public function ORIGactionDoCreate()
	{
		if(isset($_POST['Users'], $_POST['Clients']))
		{
			$response = array();
			$transaction = Yii::app()->db->beginTransaction();
			$UserSaved = false;
			$ValidUser = false;
			$ClientSaved = true;

			try {
				$UsersModel = new Users;
				$UsersModel->attributes = $_POST['Users'];
				$UsersModel->DateCreated = new CDbExpression('NOW()');
				$UsersModel->DateUpdated = new CDbExpression('NOW()');
				$UsersModel->CreatedBy = Yii::app()->user->id;
				
				if($UsersModel->AccessType === 'SUPERADMIN') 
				{
					$ClientSaved = true; // Bypass the client creation
				}
				else
				{
					$ClientsModel = new Clients;
					$ClientsModel->attributes = $_POST['Clients'];
					$ClientsModel->DateCreated = new CDbExpression('NOW()');
					$ClientsModel->DateUpdated = new CDbExpression('NOW()');
					$ClientsModel->CreatedBy = Yii::app()->user->id;

					$ClientSaved = $ClientsModel->save();

					if($ClientSaved) 
					{
						$UsersModel->ClientId = $ClientsModel->ClientId;
					}
				}

				$UserValidated = $UsersModel->validate();

				// Encryption is done in the model, beforeSave()
                //$UsersModel->Password = md5($_POST['Users']['Password']);

				if($UserValidated && $ClientSaved)
				{
					$UsersModel->save(false);
					$response = array(
						'message'=>'User successfully created.',
					);
				}
				else
				{
					if($UsersModel->AccessType === 'SUPERADMIN') 
					{
						$fieldErrors = array(
							'Users' => $UsersModel->errors
						);
					}
					else
					{
						$fieldErrors = array(
							'Clients' => $ClientsModel->errors,
							//'Users' => $UsersModel->errors
							'Users' => $UsersModel->errors
						);
					}
					

					$response = array(
						'error'=>true,
						'message'=>'User not saved.',
						'fieldErrors'=>$fieldErrors,
					);
				}
				
				$transaction->commit();
				Yii::app()->utils->sendJSONResponse($response);

			} catch (CDbException $ex) {
				$transaction->rollback();
				Yii::app()->utils->sendJSONResponse(array(
					'hasError'=>true,
					'message'=>'We have encountered an error while saving your data.',
					'data'=>$ex->getMessage(),				));
			} catch (Exception $ex) {
				$transaction->rollback();
				Yii::app()->utils->sendJSONResponse(array(
					'hasError'=>true,
					'message'=>'Oops! Something went wrong. Please try again in a few minutes.',
					'data'=>$ex->getMessage(),
				));
			}
		}
		else
		{
			Yii::app()->utils->sendJSONResponse(array(
				'hasError'=>true,
				'message'=>'Invalid action',
			));
		}
	}

	
	public function actionCreate()
	{
		$UsersModel   = new Users;
		$ClientsModel = new Clients;

		// $create_form = $this->renderPartial('_formCreate', array('UsersModel'=>$UsersModel, 'ClientsModel'=>$ClientsModel), true);

		// $this->render('create', array('model'=>$model, 'clientsModel'=>$submodel));
		// $this->render('create', array('UsersModel'=>$UsersModel, 'ClientsModel'=>$ClientsModel));
		$this->render('create', array('model'=>$UsersModel, 'submodel'=>$ClientsModel));
	}

	public function actionDelete()
	{
		$id = Yii::app()->request->getQuery('id');

		Yii::app()->utils->sendJSONResponse(array('id' => $id));
		$utilLog = new Utils;
		$utilLog->saveAuditLogs();
		$rowCount = Users::model()->findByPk($id)->delete();
		// $this->render('delete');
	}

	public function actionIndex()
	{
		// Reference for the datepicker:
		// http://mootools.net/forge/p/mootools_datepicker
		// https://github.com/arian/mootools-datepicker/blob/master/README.md
		$this->extraJS = '<script src="'. Yii::app()->request->baseUrl .'/assets/js/vendor/Locale.en-US.DatePicker.js" ></script>'.PHP_EOL;
		$this->extraJS .= '<script src="'. Yii::app()->request->baseUrl .'/assets/js/vendor/Picker.js" ></script>'.PHP_EOL;
		$this->extraJS .= '<script src="'. Yii::app()->request->baseUrl .'/assets/js/vendor/Picker.Attach.js" ></script>'.PHP_EOL;
		$this->extraJS .= '<script src="'. Yii::app()->request->baseUrl .'/assets/js/vendor/Picker.Date.js" ></script>'.PHP_EOL;
		$this->extraJS .= '<script src="'. Yii::app()->request->baseUrl .'/assets/js/Users.js" ></script>'.PHP_EOL;
		$this->extraCSS = '<link href="'. Yii::app()->request->baseUrl .'/css/vendor/datepicker.css"  rel="stylesheet">'.PHP_EOL;
		$this->mainDivClass = 'users-list-container';
		$this->modals = 'users-modals';

		// Find all non-pending users
		// $criteria = new CDbCriteria;
		// $criteria->select='UserId, Username, FirstName, LastName, AccessType, Status, DateCreated';
		// $criteria->addInCondition('status', array('ACTIVE', 'INACTIVE'));

		// $model = Users::model();
		// $users = $model->getCommandBuilder()->createFindCommand($model->tableSchema, $criteria)->queryAll();

		$_cust = Clients::model()->findAll(array(
			'select'=>'ClientId, CompanyName', 'condition'=>'status=\'ACTIVE\''));
		$cust  = array();
		foreach($_cust as $row) {
			$cust[$row->ClientId] = "{$row->CompanyName}";
		}
		
		
		// Pass true as the third parameter to assign the output to a variable.
		$createForm = $this->renderPartial('_formCreate', array('model'=>new Users, 
		'submodel'      =>new Clients,
		'client_list' => $cust), true);

		$this->render('index', array('createForm'=>$createForm,
					     'client_list' => $cust));
	}

        public function actionEdit($id)
        {
            if($_POST) {
                $UserModel = array();
                $form_fields = array('FirstName','MiddleName','LastName','Email','ContactNumber','Status','Password','ConfirmPassword');

                //@var_dump($_POST['Users']); exit;

                foreach($_POST['Users'] as $idx => $val) {
                    if( in_array($idx, $form_fields)) $UserModel[$idx] = $val;
                }

                $model = Users::model()->findByPk($id, array('select'=>'UserId, ClientId, FirstName, MiddleName, LastName, Gender, Birthdate, ContactNumber, Address, Email, Username, AccessType, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy'));

                $model->attributes = $UserModel;
                $model->UpdatedBy = Yii::app()->user->id;
                $model_validated = $model->validate();
		
		if($model_validated)
                {
                    if($model->save(false))
                    {
			$utilLog = new Utils;
			$utilLog->saveAuditLogs();

                    	Yii::app()->getUser()->setFlash('user-update-success', 'User updated.');
                    }
                    else
                    {
                    	Yii::app()->getUser()->setFlash('user-update-error', 'Failed to save changes.');
                    }
                }

            	$model->Password = '';
            	$model->ConfirmPassword = '';
                    
                $this->render('_formUpdate', array(
                    'model' => $model,
                ));

            } else {
                $model = Users::model()->findByPk($id, array('select'=>'UserId, ClientId, Username, FirstName, MiddleName, LastName, ContactNumber, Email, AccessType, Status, DateUpdated'));

                if($model) {
                    $model->DateUpdated = date('F j, Y g:i A', strtotime($model->DateUpdated));
                } else {
                    echo 'Record not found.';
                    Yii::app()->end();
                }

                $this->render('_formUpdate', array(
                    'model' => $model,
                ));
            }
        }

 	public function actionChangepass($id)
        {
            $is_superadmin = Users::model()->findByPk(Yii::app()->user->id)->AccessType === 'SUPERADMIN';
            if(!$is_superadmin)
            {
            	Yii::app()->getUser()->setFlash('user-update-error', 'Failed to save changes. (No privileges)');
            }
            
            
            if($_POST && $is_superadmin) {
                $UserModel = array();
                $form_fields = array('FirstName','MiddleName','LastName','Email','ContactNumber','Status','Password','ConfirmPassword');
		$form_fields = array('Password','ConfirmPassword');
                //echo @var_export($_POST['Users'],true); exit;

                foreach($_POST['Users'] as $idx => $val) {
                    if( in_array($idx, $form_fields)) $UserModel[$idx] = trim($val);
                }

                $model = Users::model()->findByPk($id, array('select'=>'UserId, ClientId, FirstName, MiddleName, LastName, Gender, Birthdate, ContactNumber, Address, Email, Username, AccessType, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy'));

                $model->attributes = $UserModel;
                $model->UpdatedBy = Yii::app()->user->id;
                $model_validated = $model->validate();
		
		//try to check
		$p1 = trim($_POST['Users']['Password']);
		$p2 = trim($_POST['Users']['ConfirmPassword']);
		
		if(!strlen($p1) or !strlen($p1) )
		{
			Yii::app()->getUser()->setFlash('user-update-error', 'Failed to save changes. (Please check the password/confirm-password)');
			$model_validated = false;
		}
		
		if($model_validated)
                {
                    if($model->save(false))
                    {
			$utilLog = new Utils;
			$utilLog->saveAuditLogs();

                    	Yii::app()->getUser()->setFlash('user-update-success', "User's password updated.");
                    }
                    else
                    {
                    	Yii::app()->getUser()->setFlash('user-update-error', 'Failed to save changes.');
                    }
                }
                

            	$model->Password = '';
            	$model->ConfirmPassword = '';
                    
                $this->render('_formUpdateChangePass', array(
                    'model' => $model,
                ));

            } else {
                $model = Users::model()->findByPk($id, array('select'=>'UserId, ClientId, Username, FirstName, MiddleName, LastName, ContactNumber, Email, AccessType, Status, DateUpdated'));

                if($model) {
                    $model->DateUpdated = date('F j, Y g:i A', strtotime($model->DateUpdated));
                } else {
                    echo 'Record not found.';
                    Yii::app()->end();
                }

                $this->render('_formUpdateChangePass', array(
                    'model' => $model,
                ));
            }
            
        }


	public function actionView($id)
	{
        $is_superadmin = Users::model()->findByPk(Yii::app()->user->id)->AccessType === 'SUPERADMIN';

        // 20150430: We are going to limit the view of the current user to just within the Client it belongs to if he belongs to one.
        // Otherwise, if user is a superadmin, just get the record by ID
        if($is_superadmin)
        {
            $model = Users::model()->findByPk($id, 
            array('select'=>'UserId, ClientId, Username, FirstName, MiddleName, LastName, ContactNumber, Email, AccessType, Status, DateUpdated'));
        }
        else
        {
            $criteria = new CDbCriteria;
            //$criteria->addCondition('ClientId = :clientId AND UserId = :userId');
            //$criteria->params = array(':clientId' => Users::model()->findByPk(Yii::app()->user->id)->ClientId, ':userId' => $id);
            $criteria->select = 'UserId, ClientId, Username, FirstName, MiddleName, LastName, ContactNumber, Email, AccessType, Status, DateUpdated';
            $model = Users::model()->findByAttributes(
            		array('ClientId'=> Users::model()->findByPk(Yii::app()->user->id)->ClientId,
            		      'UserId'  => $id),$criteria);
        }


		if($model) {
			$data = array(
                                'UserId'=>$model->UserId,
				'ClientId'=>$model->ClientId,
				'CompanyName'=>($model->clientInfo? $model->clientInfo->CompanyName:""),
				'Username'=>$model->Username,
				'FirstName'=>$model->FirstName,
                                'MiddleName'=>$model->MiddleName,
				'LastName'=>$model->LastName,
                                'ContactNumber'=>$model->ContactNumber,
                                'Email'=>$model->Email,
				'AccessType'=>$model->AccessType,
				'Status'=>ucwords(strtolower($model->Status)),
				'Birthdate'=>date('F j, Y', strtotime($model->Birthdate)),
                                'DateUpdated'=>date('F j, Y g:i A', strtotime($model->DateUpdated)),
			);
		}
		else {
			// redirect to not found..
			$data['error'] = 'I am unable to find that user.';
			$data['Username'] = '';
			$data['CompanyName'] = '';
			$data['created']  = '';
			$data['updated']  = '';
			
			$data['UserId'        ] = '';
			$data['Username'      ] = '';
			$data['FirstName'     ] = '';
			$data['MiddleName'    ] = '';
			$data['LastName'      ] = '';
			$data['ContactNumber' ] = '';
			$data['Email'         ] = '';
			$data['AccessType'    ] = '';
			$data['Status'        ] = '';
			$data['Birthdate'     ] = '';
			$data['DateUpdated'   ] = '';
		}
		
		if(0){
			echo Yii::app()->user->id."#HEHEHE<hr>".@var_export($model,true);
			echo Yii::app()->user->id."#HEHEHE<hr>".@var_export($data,true);
			exit;
		}
		//$this->render('view', array(
		//	'data'=>$data,
		//	));
                $this->render('view', array(
                    'data'=>$data,
                ));
	}

	public function actionUpdate()
	{
		$id = Yii::app()->request->getQuery('id');
		$UserForm = Yii::app()->request->getQuery('UserForm');

		$user = Users::model()->findByPk($id);

		if($user)
		{
			try {
				$user->attributes = $UserForm;
				$user->DateUpdated = new CDbExpression('NOW()');

				$saved = $user->save();

				if($saved) {
					$utilLog = new Utils;
					$utilLog->saveAuditLogs();

					Yii::app()->utils->sendJSONResponse( array('response'=>'success', 'message'=>'Info has been updated.'));
				}
				else {
					Yii::app()->utils->sendJSONResponse( array('response'=>'success', 'message'=>'Failed to save data.'));
				}
			}
			catch(CDbException $ex) {
				Yii::app()->utils->sendJSONResponse( array('response'=>'success', 'message'=>'An error has occured.'));
			}
			catch(Exception $ex) {
				Yii::app()->utils->sendJSONResponse( array('response'=>'success', 'message'=>'Something went wrong.'));
			}
		}

		Yii::app()->utils->sendJSONResponse( array('id' => $id, 'form'=>$UserForm));
		// $this->render('update');
	}

	public function actionList()
	{
		$limit  = Yii::app()->params['list']['perPage'];
		$page   = (int) Yii::app()->request->getParam('page', '1');
		$offset = ($page - 1) * $limit;
		$search = Yii::app()->request->getParam('search');
		$arr    = array('error'=>false, 'message'=>'Ack');

		try {
			$criteria = new CDbCriteria;
			$criteria->select="UserId, ClientId, Username, FirstName, LastName, AccessType, Status, DateCreated";

			if($search) $criteria->compare('Username', $search, true);

			$arr['totalData'] = Users::model()->count($criteria);
			$arr['totalPages'] = ceil($arr['totalData']/$limit);
			$arr['page'] = $page;

			$criteria->offset=$offset;
			$criteria->limit=$limit;

			// $arr['data'] = Users::model()->inMyCompany()->findAll($criteria);
			$arr['data'] = Users::model()->findAll($criteria);
			$arr['totalRows'] = count($arr['data']);

			if($arr['totalRows']) {
				$arr['rowLower'] = $offset + 1;
				$arr['rowUpper'] = $offset + $arr['totalRows'];
			}
			else {
				$arr['totalPages'] = 0;
				$arr['rowLower'] = 0;
				$arr['rowUpper'] = 0;
			}
		}
		catch(CDbException $ex) {
			$arr['error'] = true;
			$arr['message'] = 'CDbException: ' . $ex->getMessage();
		}
		catch(Exception $ex) {
			$arr['error'] = true;
			$arr['message'] = 'Exception' . $ex->getMessage();
		}

		Yii::app()->utils->sendJSONResponse( $arr);
	}
	// Uncomment the following methods and override them if needed
	/*
	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}
