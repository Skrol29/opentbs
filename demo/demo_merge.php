<?php

// Display this code source is asked.
if (isset($_GET['source'])) exit('<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>OpenTBS plug-in for TinyButStrong - demo source</title></head><body>'.highlight_file(__FILE__,true).'</body></html>');

// load the TinyButStrong libraries
if (version_compare(PHP_VERSION,'5')<0) {
	include_once('tbs_class_php4.php'); // TinyButStrong template engine for PHP 4
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
$suffix = (isset($_POST['suffix']) && (trim($_POST['suffix'])!=='') && ($_SERVER['SERVER_NAME']=='localhost')) ? trim($_POST['suffix']) : '';
$debug = (isset($_POST['debug']));

// Retrieve the template to use
$template = (isset($_POST['tpl'])) ? $_POST['tpl'] : '';
$template = basename($template);
$x = pathinfo($template);
$template_ext = $x['extension'];
if (substr($template,0,5)!=='demo_') exit("Wrong file.");
if (!file_exists($template)) exit("File does not exist.");

// Retrieve the name to display
$yourname = (isset($_POST['yourname'])) ? $_POST['yourname'] : '';
$yourname = trim(''.$yourname);
if ($yourname=='') $yourname = "(no name)";

// Prepare some data for the demo
$data = array();
$data[] = array('firstname'=>'Sandra', 'name'=>'Hill', 'number'=>'1523d', 'score'=>200 );
$data[] = array('firstname'=>'Roger', 'name'=>'Smith', 'number'=>'1234f', 'score'=>800 );
$data[] = array('firstname'=>'William', 'name'=>'Mac Dowell', 'number'=>'5491y', 'score'=>130 );

$x_num = 3152.456;
$x_pc = 0.2567;
$x_dt = mktime(13,0,0,2,15,2010);
$x_bt = true;
$x_bf = false;

// Load the template
$TBS->LoadTemplate($template);
$TBS->MergeBlock('a,b', $data);

if ($template_ext=='xlsx') $TBS->MergeBlock('b1,b2', $data);

// Define the name of the output file
$file_name = str_replace('.','_'.date('Y-m-d').'.',$template);

// Output as a download file (some automatic fields are merged here)
if ($debug) {
	$TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT+OPENTBS_DEBUG_XML*1, $file_name);
} elseif ($suffix==='') {
	// download
	$TBS->Show(OPENTBS_DOWNLOAD, $file_name);
} else {
	// save as file
	$file_name = str_replace('.','_'.$suffix.'.',$file_name);
	$TBS->Show(OPENTBS_FILE+TBS_EXIT, $file_name);
}


?>