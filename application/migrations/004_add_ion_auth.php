<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ion_auth extends CI_Migration {

	public function up(){
	
		//create groups table
		$this->dbforge->add_field('id');
	
		$this->dbforge->add_field(array(
			'name' => array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '20',
			),
			'description' => array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '100',
			),
		));

		$this->dbforge->create_table('groups');
		
		//insert first group
		
		$data = array(
			'id'			=> 1,
			'name'			=> 'admin',
			'description'	=> 'Administrator',
		);
		
		$this->db->insert('groups', $data);
		
		//insert second group
		
		$data = array(
			'id'			=> 2,
			'name'			=> 'members',
			'description'	=> 'General User',
		);
		
		$this->db->insert('groups', $data);
		
		//create users table
		
		$this->dbforge->add_field('id');
	
		$this->dbforge->add_field(array(
			'ip_address'	=> array(
				'type'			=> 'VARBINARY',
				'constraint'	=> '16',
			),
			'username'		=> array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '100',
			),
			'password'		=> array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '80',
			),
			'salt'			=> array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '40',
				'default'		=> null,
			),
			'email'	 	=> array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '100',
			),
			'activation_code'	 => array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '40',
				'default'		=> null,
			),
			'forgotten_password_code'		=> array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '40',
				'default'		=> null,
			),
			'forgotten_password_time'	 	=> array(
				'type'			=> 'INT',
				'constraint'	=> '11',
				'default'		=> null,
				'unsigned'		=> true,
			),
			'remember_code'	 	=> array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '40',
				'default'		=> null,
			),
			'created_on'	 	=> array(
				'type'			=> 'INT',
				'constraint'	=> '11',
				'unsigned'		=> true,
			),
			'last_login'	 	=> array(
				'type'			=> 'INT',
				'constraint'	=> '11',
				'default'		=> null,
				'unsigned'		=> true,
			),
			'active'	 	=> array(
				'type'			=> 'TINYINT',
				'constraint'	=> '1',
				'default'		=> null,
				'unsigned'		=> true,
			),
			'first_name'	 	=> array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '50',
				'default'		=> null,
			),
			'last_name'	 	=> array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '50',
				'default'		=> null,
			),
			'company'	 	=> array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '100',
				'default'		=> null,
			),
			'phone'	 	=> array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '20',
				'default'		=> null,
			),
		));

		$this->dbforge->create_table('users');
		
		$data = array(
			'id'			=> 1,
			'ip_address'	=> 0x7f000001,
			'username'		=> 'administrator',
			'password'		=> '59beecdf7fc966e2f17fd8f65a4a9aeb09d4a3d4',
			'salt'		=> '9462e8eee0',
			'email'		=> 'admin@admin.com',
			'activation_code'		=> '',
			'forgotten_password_code'		=> NULL,
			'created_on'		=> '1268889823',
			'last_login'		=> '1268889823',
			'active'		=> '1',
			'first_name'		=> 'Admin',
			'last_name'		=> 'istrator',
			'company'		=> 'ADMIN',
			'phone'		=> '0',
		);
		
		$this->db->insert('users', $data);
		
		//create users groups
		
		$this->dbforge->add_field('id');
	
		$this->dbforge->add_field(array(
			'user_id' => array(
				'type'			=> 'MEDIUMINT',
				'constraint'	=> '8',
				'unsigned'	=> true,
			),
			'group_id' => array(
				'type'			=> 'MEDIUMINT',
				'constraint'	=> '8',
				'unsigned'	=> true,
			),
		));

		$this->dbforge->create_table('users_groups');
		
		$data = array(
			'id'	=> 1,
			'user_id'	=> 1,
			'group_id'	=> 1,
		);
		
		$this->db->insert('users_groups', $data);
		
		$data = array(
			'id'	=> 2,
			'user_id'	=> 2,
			'group_id'	=> 2,
		);
		
		$this->db->insert('users_groups', $data);
		
		//create login_attempts
		
		$this->dbforge->add_field('id');
	
		$this->dbforge->add_field(array(
			'ip_address' => array(
				'type'			=> 'VARBINARY',
				'constraint'	=> '16',
			),
			'login' => array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '100',
			),
			'time' => array(
				'type'			=> 'INT',
				'constraint'	=> '11',
				'unsigned'	=> true,
				'default'	=> null,
			),
		));

		$this->dbforge->create_table('login_attempts');
	
	}

	public function down(){
	
		//dropping all tables
		$this->dbforge->drop_table('groups');
		$this->dbforge->drop_table('users');
		$this->dbforge->drop_table('users_groups');
		$this->dbforge->drop_table('login_attempts');
	
	}
	
}