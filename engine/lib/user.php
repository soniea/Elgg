<?php

class User extends Entity
{
	public function __construct( $id=NULL )
	{
		global $CONFIG;
		$this->table_name = "{$CONFIG->dbprefix}users_entity";

		// set primary key
		$this->primary_key = 'guid';

		// get desired row
		$this->elements = $this->retrieve( $id );
	}
}