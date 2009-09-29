<?php

class ElggCoreUserTest extends ElggCoreUnitTest
{
	protected $db_prefix;

	public function __construct()
	{
		parent::__construct();

		global $CONFIG;
		$this->db_prefix = $CONFIG->dbprefix;

		$this->create_user();
	}
	
	public function __destruct()
	{
		delete_data( "DELETE FROM {$this->db_prefix}users_entity WHERE guid = '{$this->user->get_id()}'" );
		delete_data( "DELETE FROM {$this->db_prefix}entities WHERE guid = '{$this->user->get_id()}'" );
		
		parent::__destruct();
	}

	public function testElements()
	{
		$count = 0;

		$elements = get_data_row( "SELECT * FROM {$this->db_prefix}users_entity WHERE guid = '{$this->user->get_id()}'" );
		foreach ( $elements as $element => $value )
		{
			$this->assertEqual( $this->user->$element, $value );
			$count++;
		}

		$this->assertEqual( $count, count( $this->user->get_elements() ));
	}


	protected function create_user( $name='Unit Test', $username='utest', $password='testing', $salt='' )
	{
		$time_created = date( 'U' );
		$last_action = date( 'U', strtotime( '-4 hours' ));
		$prev_last_action = date( 'U', strtotime( '-12 hours' ));
		$last_login = date( 'U', strtotime( '-1 hour' ));
		$prev_last_login = date( 'U', strtotime( '-2 hours' ));
		
		$sql = "INSERT INTO {$this->db_prefix}entities
					(type, subtype, owner_guid, site_guid, container_guid, access_id, time_created, time_updated)
				VALUES
					('user', '0', '0', '1', '0', '2', '$time_created', '$time_created')";
		$user_id = insert_data( $sql );

		$sql = "INSERT INTO {$this->db_prefix}users_entity
					(guid, name, username, password, salt, email, code, last_action, prev_last_action, last_login, prev_last_login)
				VALUES
					('$user_id', '$name', '$username', md5('$password'), '$salt', 'unit@test.com', md5('userTest'), '$last_action', '$prev_last_action', '$last_login', '$prev_last_login')";
		insert_data( $sql );

		$this->user = new db_record( 'elggusers_entity', $user_id, 'guid' );
	}
}
