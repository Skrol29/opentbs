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
$suffix = (isset($_POST['suffix']) && (trim($_POST['suffix'])!=='') && ($_SERVER['SERVER_NAME']=='localhost')) ? trim($_POST['suffix']) : '';
$debug = (isset($_POST['debug']));

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
$x = -350.499;
$data = array();
$data[] = array('firstname'=>'Sandra', 'name'=>'Hill', 'number'=>'1523d' );
$data[] = array('firstname'=>'Roger', 'name'=>'Smith', 'number'=>'1234f' );
$data[] = array('firstname'=>'William', 'name'=>'Mac Dowell', 'number'=>'5491y' );

// debug
$OpenTBS =& $TBS->_PlugIns[OPENTBS_PLUGIN];

// Load the template
$TBS->LoadTemplate($template);

// TEST du nettoyage du code XML
include('fct_ms_clean_proof.php');
//$TBS->Source = '<coucou><w:r><w:rPr><w:color w:val="800000"/><w:lang w:val="en-US"/></w:rPr><w:t>2)</w:t></w:r><w:r><w:rPr><w:color w:val="800000"/><w:lang w:val="en-US"/></w:rPr><w:tab/><w:t>Each time you enter a new TBS tag, select it and click on the Review ribbon, in the Proofi</w:t></w:r><w:r><w:rPr><w:color w:val="800000"/><w:lang w:val="en-US"/></w:rPr><w:t xml:space="preserve">ng group, click the Set Language button. In the dialog, make sure the "Do not check</w:t></w:r><w:r><w:rPr><w:color w:val="800000"/><w:lang w:val="en-US"/></w:rPr><w:br/><w:t>spelling or grammar" box is checked.</w:t></w:r></coucou>';
f_CleanRsID($TBS->Source);
f_CleanProof($TBS->Source);
f_CleanMisc($TBS->Source);
f_CleanDuplicatedLayout($TBS->Source);
//$TBS->Source = $OpenTBS->XmlFormat($TBS->Source);
//echo $TBS->Source; exit;

//$OpenTBS->OpenXML_InitMap();
//echo var_export($OpenTBS->OpenXmlMap,true); exit;


$TBS->MergeBlock('a,b', $data);

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