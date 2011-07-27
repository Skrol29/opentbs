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
$debug = (isset($_POST['debug'])) ? intval($_POST['debug']) : 0;

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
$data[] = array('firstname'=>'Sandra' , 'name'=>'Hill'      , 'number'=>'1523d', 'score'=>200, 'email_1'=>'sh@tbs.com',  'email_2'=>'sandra@tbs.com',  'email_3'=>'s.hill@tbs.com');
$data[] = array('firstname'=>'Roger'  , 'name'=>'Smith'     , 'number'=>'1234f', 'score'=>800, 'email_1'=>'rs@tbs.com',  'email_2'=>'robert@tbs.com',  'email_3'=>'r.smith@tbs.com' );
$data[] = array('firstname'=>'William', 'name'=>'Mac Dowell', 'number'=>'5491y', 'score'=>130, 'email_1'=>'wmc@tbs.com', 'email_2'=>'william@tbs.com', 'email_3'=>'w.m.dowell@tbs.com' );

$x_num = 3152.456;
$x_pc = 0.2567;
$x_dt = mktime(13,0,0,2,15,2010);
$x_bt = true;
$x_bf = false;

// Load the template
$TBS->LoadTemplate($template);

if ($debug==2) { // debug mode 2
	$TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT);
	exit;
} elseif ($debug==1) { // debug mode 1
	$TBS->Plugin(OPENTBS_DEBUG_INFO);
	exit;
}

// Merge data
$TBS->MergeBlock('a,b', $data);

// specific merges depending to the docuement
if ($template_ext=='xlsx') {
	// merge cells (exending columns)
	$TBS->MergeBlock('cell1,cell2', $data);
	// change the current sheet
	$TBS->PlugIn(OPENTBS_SELECT_SHEET, 2);
	// merge data in Sheet 2
	$TBS->MergeBlock('cell1,cell2', 'num', 3);
	$TBS->MergeBlock('b2', $data);
} elseif ($template_ext=='ods') {
	// no need to change the current sheet, they are all stored in the same XLS subfile.
	// merge data in Sheet 2
	$TBS->MergeBlock('cell1,cell2', 'num', 3);
	$TBS->MergeBlock('b2', $data);
} elseif ($template_ext=='docx') {
	// change chart series
	$ChartNameOrNum = 'chart1';
	$SeriesNameOrNum = 2;
	$NewValues = array( array('Category A','Category B','Category C','Category D'), array(3, 1.1, 4.0, 3.3) );
	$NewLegend = "New series 2";
	$TBS->PlugIn(OPENTBS_CHART, $ChartNameOrNum, $SeriesNameOrNum, $NewValues, $NewLegend);
}

// Define the name of the output file
$file_name = str_replace('.','_'.date('Y-m-d').'.',$template);

// Output as a download file (some automatic fields are merged here)
if ($debug==3) { // debug mode 3
	$TBS->Plugin(OPENTBS_DEBUG_XML_SHOW);
} elseif ($suffix==='') {
	// download
	$TBS->Show(OPENTBS_DOWNLOAD, $file_name);
} else {
	// save as file
	$file_name = str_replace('.','_'.$suffix.'.',$file_name);
	$TBS->Show(OPENTBS_FILE+TBS_EXIT, $file_name);
}
