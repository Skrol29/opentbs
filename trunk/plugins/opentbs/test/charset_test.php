<?php

// Display this code source is asked.

// Libraries
if (version_compare(PHP_VERSION,'5')<0) {
	include_once('tbs_class.php'); // TinyButStrong template engine for PHP 4
} else {
	include_once('tbs_class_php5.php'); // TinyButStrong template engine
}
include_once('../trunk/tbs_plugin_opentbs.php'); // OpenTBS plugin

$TBS = new clsTinyButStrong; // new instance of TBS
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

// Retrieve the template to use
$template = 'demo_oo_text.odt';
if (!file_exists($template)) exit("File does not exist.");

// Retrieve the name to display
// Here is a Polish UTF8 string!
$yourname = 'Giełda Mediowa - ąęćłśóńźżĄĘŁÓŃŻŹĆŚ';

echo $yourname . "\n";

$data = array();
$data[] = array('firstname'=>'Sandra', 'name'=>'Hill', 'number'=>'1523d' );
$data[] = array('firstname'=>'Roger', 'name'=>'Smith', 'number'=>'1234f' );
$data[] = array('firstname'=>'William', 'name'=>'Mac Dowell', 'number'=>'5491y' );

// Load the template
//$TBS->LoadTemplate($template,'utf-8');
$TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

// Prepare some data for the demo

// Load the template
$TBS->MergeBlock('a', $data);

// Define the name of the output file
$file_name = 'wynik.ods';

// Output as a download file (some automatic fields are merged here)
//$TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, $file_name);

// Save as file on the disk (code example)
$TBS->Show(OPENTBS_FILE+TBS_EXIT, $file_name);

?>
