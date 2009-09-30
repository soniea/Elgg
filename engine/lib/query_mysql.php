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
class MySqlDriver extends Query
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
}
