<?php

// Display this code source is asked.
if (isset($_GET['source'])) exit(highlight_file(__FILE__,true));

// load the TinyButStrong libraries
if (version_compare(PHP_VERSION,'5')<0) {
	include_once('tbs_class.php'); // TinyButStrong template engine for PHP 4
} else {
	include_once('tbs_class_php5.php'); // TinyButStrong template engine
}

// load the OpenTBS plugin
if (file_exists('tbs_plugin_opentbs.php')) {
	include_once('tbs_plugin_opentbs.php');
} else {
	include_once('../tbs_plugin_opentbs.php');
}

$TBS = new clsTinyButStrong; // new instance of TBS
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

// Read parameters
if (!isset($_POST['btn_go'])) exit("You must use demo.html");

// Retrieve the template to use
$template = (isset($_POST['tpl'])) ? $_POST['tpl'] : '';
$template = basename($template);
if (substr($template,0,5)!=='demo_') exit("Wrong file.");
if (!file_exists($template)) exit("File does not exist.");

// Retrieve the name to display
$yourname = (isset($_POST['yourname'])) ? $_POST['yourname'] : '';
$yourname = trim(''.$yourname);
if ($yourname=='') $yourname = "(no name)";

// Prepare some data for the demo
$data = array();
$data[] = array('firstname'=>'Sandra', 'name'=>'Hill', 'number'=>'1523d' );
$data[] = array('firstname'=>'Roger', 'name'=>'Smith', 'number'=>'1234f' );
$data[] = array('firstname'=>'William', 'name'=>'Mac Dowell', 'number'=>'5491y' );

// Load the template
$TBS->LoadTemplate($template);
$TBS->MergeBlock('a,b', $data);

// Define the name of the output file
$file_name = str_replace('.','_'.date('Y-m-d').'.',$template);

// Output as a download file (some automatic fields are merged here)
$TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, $file_name);

// Save as file on the disk (code example)
//$TBS->Show(OPENTBS_FILE+TBS_EXIT, $file_name);

?>