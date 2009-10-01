<?php

/**
 * Elgg Database Object
 *
 * @package Elgg
 * @subpackage Database
 * @author Curverider Ltd
 * @link http://elgg.org
 * @since Version 1.7
 */
abstract class ElggDatabase
{
	abstract protected function db_connect( $server, $username, $password );
	abstract protected function db_select( $database );

	public function __construct()
	{
		global $CONFIG;
		
		$this->connect_to_database( $CONFIG->dbhost, $CONFIG->dbuser, $CONFIG->dbpass );
		$this->select_database( $CONFIG->dbname );
	}

	public function connect_to_database( $server, $username, $password )
	{
		if ( !$this->db_connect( $server, $username, $password ))
		{
			throw new DatabaseException( elgg_echo( 'DatabaseException:WrongCredentials' ));
		}
	}

	public function select_database( $database )
	{
		if ( !$this->db_select( $database ))
		{
			throw new DatabaseException( sprintf( elgg_echo( 'DatabaseException:NoConnect' ), $database ));
		}
	}
}
