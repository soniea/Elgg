<?php

/**
 * Elgg Core Database Test
 *
 * @package Elgg
 * @subpackage Unit Test
 * @author Curverider Ltd
 * @link http://elgg.org
 * @since Version 1.7
 */
class ElggCoreDatabaseTest extends ElggCoreUnitTest
{
	public function testMySqlDriverConnection()
	{
		// attempt to connect to invalid database
		$this->invalid_mysql_connection();

		// attempt to select invalid database
		$this->invalid_mysql_database();

		// ensure proper connection
		$query = new MySqlDriver();
		$this->assertIsA( $query, 'MySqlDriver' );
	}


	protected function invalid_mysql_database( $database='elgg_invalid' )
	{
		$original = $this->overwrite_config( 'dbname', $database );
		$this->catch_exception( 'DatabaseException', sprintf( elgg_echo( 'DatabaseException:NoConnect' ), $database ));
		$this->overwrite_config( 'dbname', $original );
	}

	protected function invalid_mysql_connection( $host='localhost', $user='elgg_invalid', $pass='12345' )
	{
		$old_host = $this->overwrite_config( 'dbhost', $host );
		$old_user = $this->overwrite_config( 'dbuser', $user );
		$old_pass = $this->overwrite_config( 'dbpass', $pass );

		$this->catch_exception( 'DatabaseException', elgg_echo( 'DatabaseException:WrongCredentials' ));

		$this->overwrite_config( 'dbhost', $old_host );
		$this->overwrite_config( 'dbuser', $old_user );
		$this->overwrite_config( 'dbpass', $old_pass );
	}

	protected function overwrite_config( $name, $value )
	{
		global $CONFIG;

		$original = $CONFIG->$name;
		$CONFIG->$name = $value;

		return $original;
	}

	protected function catch_exception( $class, $message )
	{
		try
		{
			$query = new MySqlDriver();
			$this->assertTrue( FALSE );
		}
		catch ( Exception $e )
		{
			$this->assertIsA( $e, $class );
			$this->assertIdentical( $e->getMessage(), $message );
		}
	}
}
