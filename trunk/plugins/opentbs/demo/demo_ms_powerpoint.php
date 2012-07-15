<?php

// Load the TinyButStrong template engine
include_once('tbs_class.php');

// Load the OpenTBS plugin
include_once('../tbs_plugin_opentbs.php');

$TBS = new clsTinyButStrong; // new instance of TBS
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

// Options
if (!isset($suffix))    $suffix = ''; // Donwload mode
if (!isset($debug))     $debug = 0;   // Debug Mode disabled

// Prepare some data for the demo
// ------------------------------
if (!isset($yourname))  $yourname = '(no name)';

$data = array();
$data[] = array('firstname'=>'Sandra' , 'name'=>'Hill'      , 'number'=>'1523d', 'score'=>200, 'email_1'=>'sh@tbs.com',  'email_2'=>'sandra@tbs.com',  'email_3'=>'s.hill@tbs.com');
$data[] = array('firstname'=>'Roger'  , 'name'=>'Smith'     , 'number'=>'1234f', 'score'=>800, 'email_1'=>'rs@tbs.com',  'email_2'=>'robert@tbs.com',  'email_3'=>'r.smith@tbs.com' );
$data[] = array('firstname'=>'William', 'name'=>'Mac Dowell', 'number'=>'5491y', 'score'=>130, 'email_1'=>'wmc@tbs.com', 'email_2'=>'william@tbs.com', 'email_3'=>'w.m.dowell@tbs.com' );

$x_num = 3152.456;
$x_pc = 0.2567;
$x_dt = mktime(13,0,0,2,15,2010);
$x_bt = true;
$x_bf = false;
$x_delete = 1;

// Load the template
// -----------------

$template = 'demo_ms_powerpoint.pptx';
$TBS->LoadTemplate($template); // Also merge some [onload] automatic fields (depdens of the type of document).

// Debug mode
if ($debug==2) {
	$TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT);
	exit;
} elseif ($debug==1) {
	$TBS->Plugin(OPENTBS_DEBUG_INFO);
	exit;
}

// Merging and other operations on the template
// --------------------------------------------

// Merge data in the first slide of the presentation
$TBS->MergeBlock('a,b', $data);

// Output the result
// -----------------

// Define the name of the output file
$file_name = str_replace('.', '_'.date('Y-m-d').'.', $template);

if ($debug==3) {
	// Debug mode
	$TBS->Plugin(OPENTBS_DEBUG_XML_SHOW);
} elseif ($suffix==='') {
	// Output the result as a downloadable file (only streaming, no data saved in the server)
	$TBS->Show(OPENTBS_DOWNLOAD, $file_name); // Also merges all [onshow] automatic fields.
} else {
	// Output the result as a file on the server
	$file_name = str_replace('.', '_'.$suffix.'.', $file_name);
	$TBS->Show(OPENTBS_FILE+TBS_EXIT, $file_name); // Also merges all [onshow] automatic fields.
}
