<?php
/**
 * A Simple Database Abstraction Model.
 * designed with the Relational Model in mind.
 *
 * db_record represents a mathematical tuple t within the relation r of the
 * relation schema R, such that:
 *
 *	 t = <v1, v2, ..., vn>, where r = {t1, t2, ..., tm} given R(A1, A2, ..., An).
 *
 * In other words, db_record is an instantiation of a single row of values within
 * a database table.
 *
 * @author Nick Whitt <nick@elgg.com>
 * @package db_record
 */

class db_record
{
	protected $table_name;
	protected $primary_key;
	protected $elements=array();
	
	public function __construct( $table, $id=NULL, $primary_key='id' )
	{
		$this->table_name = $table;

		// set primary key
		$this->primary_key = $primary_key;

		// get desired row
		$this->elements = $this->retrieve( $id );
	}
	
	public function get_id()
	{
		return $this->__get( $this->primary_key );
	}
	
	public function get_elements()
	{
		return $this->elements;
	}
	
	
	/*******************************************
		Database Manipulation Methods
	 *******************************************/
	
	public function create( $elements=NULL )
	{
		$this->set_elements( $elements );
		
		$attributes = implode( ', ', array_keys( $this->elements ));
		$values = implode( '", "', $this->elements );
		if ( !get_data_array( "INSERT INTO `$this->table_name` ($attributes) VALUES (\"$values\")" ))
		{
			return FALSE;
		}
		
		return $this->__set( $this->primary_key, mysql_insert_id() );
	}
	
	public function retrieve( $id )
	{
		if ( $rset = get_data_array( "SELECT * FROM `$this->table_name` WHERE `$this->primary_key` = \"$id\"" ))
		{
			return $rset[0];
		}
		
		$elements = array();
		foreach ( get_data_array( "SHOW COLUMNS FROM `$this->table_name`" ) as $row )
		{
			$elements[$row['Field']] = $row['Default'];
		}
		
		return $elements;
	}
	
	public function update( $elements=NULL )
	{
		$this->set_elements( $elements );
		
		$values = get_sql_update_values( $this->elements );
		return get_data_array( "UPDATE `$this->table_name` SET $values WHERE `$this->primary_key` = \"{$this->get_id()}\"" );
	}
	
	public function delete()
	{
		if ( !get_data_array( "DELETE FROM `$this->table_name` WHERE `$this->primary_key` = \"{$this->get_id()}\"" ))
		{
			return FALSE;
		}
		
		$this->__construct();
		return TRUE;
	}
	
	
	/*******************************************
		Magic Methods
	 *******************************************/
	
	public function __get( $column )
	{
		if ( !$this->is_valid_attribute( $column ))
		{
			// @todo error
			return FALSE;
		}
		
		return $this->elements[$column];
	}
	
	public function __set( $attribute, $value )
	{
		$this->elements[$attribute] = $value;
		
		return TRUE;
	}
	
	
	/*******************************************
		Protected Helper Methods
	 *******************************************/
	
	protected function is_valid_attribute( $attribute )
	{
		return array_key_exists( $attribute, $this->elements );
	}
	
	protected function set_elements( array $elements )
	{
		foreach ( $elements as $attribute => $element )
		{
			if ( $this->is_valid_attribute( $attribute ))
			{
				$this->__set( $attribute, $element );
			}
		}
	}
}