<?php

require_once( dirname( __FILE__ ) . '/../start.php' );

$vendor_path = "$CONFIG->path/vendors/simpletest";
$test_path = "$CONFIG->path/engine/tests";

require_once( "$vendor_path/unit_tester.php" );
require_once( "$vendor_path/reporter.php" );
require_once( "$test_path/elgg_unit_test.php" );

$suite = new TestSuite( 'Elgg Core Unit Tests' );

$suite->addTestFile( "$test_path/user.php" );

if ( TextReporter::inCli() )
{
    exit( $suite->Run( new TextReporter() ) ? 0 : 1 );
}
$suite->Run( new HtmlReporter() );
