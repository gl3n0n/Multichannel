<?php

class m150204_165725_create_users_table extends CDbMigration
{
	protected $mySqlOptions = 'ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci';

	public function up()
	{
		// users
		$this->createTable('users', array(
			'id'=>'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
			'username'=>'varchar(32) NOT NULL',
			'password'=>'varchar(64)',
			'date_created'=>'DATETIME DEFAULT \'0000-00-00 00:00:00"\'',
			'date_updated'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
			'PRIMARY KEY (id)',
			'UNIQUE KEY username_udx (username)'
		), $this->mySqlOptions);

		// we can put this into a text file and load it up instead of declaring here.
		// example: a CSV file that looks like this:
		//     admin,admin
		//     staff,staff
		$default_data = array(
			array('username'=>'admin',		'password'=>md5('admin')),
			array('username'=>'staff',		'password'=>md5('staff')),
			array('username'=>'minion',		'password'=>md5('minion')),
			array('username'=>'user',		'password'=>md5('user')),
			array('username'=>'hacker',		'password'=>md5('hacker')),
			array('username'=>'spammer',	'password'=>md5('spammer')),
			array('username'=>'bigboss',	'password'=>md5('bigboss')),
			array('username'=>'fireman',	'password'=>md5('fireman')),
			array('username'=>'pet',		'password'=>md5('pet')),
			array('username'=>'bossman',	'password'=>md5('bossman')),
			array('username'=>'spy',		'password'=>md5('spy')),
			array('username'=>'gru',		'password'=>md5('gru')),
			array('username'=>'musika',		'password'=>md5('musika')),
			array('username'=>'jester',		'password'=>md5('jester')),
			array('username'=>'pirateking',	'password'=>md5('pirateking')),
			array('username'=>'barbie',		'password'=>md5('barbie')),
			array('username'=>'dinohunter',	'password'=>md5('dinohunter')),
			array('username'=>'hermit',		'password'=>md5('hermit')),
			array('username'=>'monk',		'password'=>md5('monk')),
			array('username'=>'barista',	'password'=>md5('barista')),
			array('username'=>'jockey',		'password'=>md5('jockey')),
			array('username'=>'shawn',		'password'=>md5('shawn')),
			array('username'=>'typist',		'password'=>md5('typist')),
			array('username'=>'barbers',	'password'=>md5('barbers')),
			array('username'=>'queen',		'password'=>md5('queen')),
			array('username'=>'shaman',		'password'=>md5('shaman')),
			array('username'=>'antoniette',	'password'=>md5('antoniette')),
			array('username'=>'keeper',		'password'=>md5('keeper')),
			array('username'=>'dusty',		'password'=>md5('dusty')),
			array('username'=>'cook',		'password'=>md5('cook')),
			array('username'=>'rocker',		'password'=>md5('rocker')),
			array('username'=>'poring',		'password'=>md5('poring')),
			array('username'=>'racer',		'password'=>md5('racer')),
			array('username'=>'somber',		'password'=>md5('somber')),
			array('username'=>'hien',		'password'=>md5('hien')),
			array('username'=>'steward',	'password'=>md5('steward')),
			);

		foreach ($default_data as &$row) {
			$row['date_created'] = new CDbExpression('NOW()');
		}

		$this->insertMultiple('users', $default_data);

		// $this->insert('users', array(
		// 	'username'=>'admin',
		// 	'password'=>md5('admin'),
		// 	'date_created'=>new CDbExpression('NOW()')
		// ));
	}

	public function down()
	{
		// echo "m150204_165725_create_users_table does not support migration down.\n";
		// return false;
		// $this->dropIndex('username_udx', 'users');

		$this->dropTable('users');
	}

	
	// Use safeUp/safeDown to do migration with transaction
	/*
	public function safeUp()
	{
		// users
		$this->createTable('users', array(
			'id'=>'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
			'username'=>'varchar(32) NOT NULL',
			'password'=>'varchar(64)',
			'date_created'=>'DATETIME DEFAULT \'0000-00-00 00:00:00"\'',
			'date_updated'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
			'PRIMARY KEY (id)',
			'UNIQUE KEY username_udx (username)'
		), $this->mySqlOptions);

		$this->insert('users', array(
			'username'=>'admin',
			'password'=>md5('admin'),
			'date_created'=>new CDbExpression('NOW()')
		));
	}

	public function safeDown()
	{
		$this->dropTable('users');
	}
	*/
}