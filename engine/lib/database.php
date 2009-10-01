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
	
	abstract protected function create( $sql );
	abstract protected function retrieve( $sql );
	abstract protected function update( $sql );
	abstract protected function delete( $sql );
	
	abstract protected function sanitize_sql( $sql );
	abstract protected function execute_sql( $sql );
	abstract protected function get_error();
	abstract protected function to_array( $result );

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
	
	public function query( $sql )
	{
		// process the transaction through the driver
		$result = $this->execute_sql( $sql );
		if ( $error = $this->get_error() )
		{
			throw new DatabaseException( "$error QUERY: $sql" );
		}
		
		// convert to ElggResult
		return $this->to_array( $result );
	}
}
