<?php

/**
 * MySQL Driver
 *
 * @package Elgg
 * @subpackage Database
 * @author Curverider Ltd
 * @link http://elgg.org
 * @since Version 1.7
 */
class MySqlDriver extends ElggDatabase
{
	protected $persistent_connection=TRUE;
	protected $link_identifier;

	protected function db_connect( $server, $username, $password )
	{
		return $this->link_identifier = @mysql_connect( $server, $username, $password, !$this->persistent_connection, $client_flags=0 );
	}

	protected function db_select( $database )
	{
		return @mysql_select_db( $database, $this->link_identifier );
	}
	
	protected function sanitize_sql( $sql )
	{
		return @mysql_real_escape_string( $sql, $this->link_identifier );
	}
	
	protected function create( $sql )
	{
		return $this->execute_sql( $sql );
	}
	
	protected function retrieve( $sql )
	{
		return $this->execute_sql( $sql );
	}
	
	protected function update( $sql )
	{
		return $this->execute_sql( $sql );
	}
	
	protected function delete( $sql )
	{
		return $this->execute_sql( $sql );
	}
	
	protected function execute_sql( $sql )
	{
		return @mysql_query( $sql, $this->link_identifier );
	}
	
	protected function get_error()
	{
		return @mysql_error( $this->link_identifier );
	}
	
	protected function to_array( $result )
	{
		$rows = array();
		while ( $row = mysql_fetch_assoc( $result ))
		{
			$rows[] = $row;
		}
		
		return $rows;
	}
}
