<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL ^ E_DEPRECATED);

if (defined('E_DEPRECATED')) {
	error_reporting(E_ALL ^ E_DEPRECATED);
} else {
	error_reporting(E_ALL);
}

class AllTests_Loader
{
	protected static $path;

	public static function register ($path)
	{
		self::$path = realpath($path) . '/';
		spl_autoload_register(array(__CLASS__, 'load'));
	}

	public static function unregister ()
	{
		spl_autoload_unregister(array(__CLASS__, 'load'));
	}

	public static function load ($class)
	{
		$file = str_replace('_', '/', $class);
		@include self::$path . $file . '.php';
	}
}

AllTests_Loader::register(dirname(__FILE__) . '/../lib/');

require_once dirname(__FILE__) . '/simpletest/unit_tester.php';
require_once dirname(__FILE__) . '/simpletest/mock_objects.php';
require_once dirname(__FILE__) . '/simpletest/collector.php';

class AllTests extends TestSuite {
	function AllTests() {
		$this->TestSuite('All tests');
		$this->addTestFile(dirname(__FILE__) . '/Tests/Gwilym/Request.php');
		$this->addTestFile(dirname(__FILE__) . '/Tests/Gwilym/Router/Default.php');
		$this->addTestFile(dirname(__FILE__) . '/Tests/Gwilym/UriParser/Fixed.php');
		$this->addTestFile(dirname(__FILE__) . '/Tests/Gwilym/UriParser/Guess.php');
	}
}

$suite = new AllTests();

if (@$_GET['coverage']) {
	$filter = PHP_CodeCoverage_Filter::getInstance();
	$filter->addDirectoryToWhitelist(dirname(dirname(__FILE__)) . '/lib/Gwilym');
	$filter->addDirectoryToBlacklist(dirname(__FILE__));

	$coverage = new PHP_CodeCoverage();
	$coverage->start('UnitTests');
}

$suite->run(new DefaultReporter());

if (@$_GET['coverage']) {
	$coverage->stop();

	$report = dirname(__FILE__) . '/coverage/' . date('Y_m_d-H_i_s');
	mkdir($report);

	$writer = new PHP_CodeCoverage_Report_HTML;
	$writer->process($coverage, $report);

	$report = dirname(__FILE__) . '/coverage/current';
	@mkdir($report);
	$writer->process($coverage, $report);
}
