<?php

// Retrieve the file to open
$file = (isset($_POST['file'])) ? $_POST['file'] : '';
$file = basename($file); // for security
$info = pathinfo($file);

// Checks
if (substr($file, 0, 10) !== 'demo_read_') exit("Wrong file.");
if (!file_exists($file)) exit("The asked file does not exist.");

// Button template
if (isset($_POST['btn_template'])) {
	header('Location: '.$file); 
	exit;
}

// Button script
if (isset($_POST['btn_script'])) {
	f_source(__FILE__);
	exit;
}

// Start the demo

include_once('tbs_class.php');
include_once('../tbs_plugin_opentbs.php');

echo "<!DOCTYPE html>";
echo "\n<html lang='en'>";
echo "\n<body>";
echo "\n<h1>Read dada in file with OpenTBS</h1>";
echo "\n<h2>File: {$file}</h2>";

$TBS = new clsTinyButStrong();
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);

$TBS->LoadTemplate($file);

// ------------------------
// Read ranges
// ------------------------

$options = array(
	'noerr' => true,
	//'rangeinfo' => true,
);

$range = 'B19:C22';
$data = $TBS->Plugin(OPENTBS_GET_CELLS, $range, $options);
f_display_result($range, $data, false, "Example: unamed range");

$range = 'myrange_all_types';
$data = $TBS->Plugin(OPENTBS_GET_CELLS, $range, $options);
f_display_result($range, $data, false, "Example: all cell types");

$range = 'merged_horizontal_1';
$data = $TBS->Plugin(OPENTBS_GET_CELLS, $range, $options);
f_display_result($range, $data, false, "Example: range with cells merged horizontally");

$range = 'merged_horizontal_2';
$data = $TBS->Plugin(OPENTBS_GET_CELLS, $range, $options);
f_display_result($range, $data, false, "Example: range with cells merged horizontally (twice)");

$range = 'merged_vertical_1';
$data = $TBS->Plugin(OPENTBS_GET_CELLS, $range, $options);
f_display_result($range, $data, false, "Example: range with cells merged vertically");

$range = 'infinit_vertical_1';
$data = $TBS->Plugin(OPENTBS_GET_CELLS, $range, $options);
f_display_result($range, $data, false, "Example: range having full columns");

$range = 'infinit_horizontal_1';
$data = $TBS->Plugin(OPENTBS_GET_CELLS, $range, $options);
f_display_result($range, $data, false, "Example: range having full rows");


// ------------------------
// Test options
// ------------------------
$options_lst = array(
	array('header' => true),
	array('del_blank_rows' => true),
	array('header' => true, 'del_blank_rows' => true),
	array('header' => true, 'columns' => array('aaa', 'bbb', 'ccc')),
	array('header' => true, 'columns' => array('aaa', null, 'ccc')),
	array('header' => true, 'columns' => array('Id' => 'id_item')),
	array('header' => true, 'row_max' =>16),
);
foreach ($options_lst as $options) {
	$range = 'test_options';
	$data = $TBS->Plugin(OPENTBS_GET_CELLS, $range, $options);
	f_display_result($range, $data, $options, "Test options");
}

echo "\n</body>";
echo "\n</html>";
exit;

// ------------------------
// Useful functions
// ------------------------

// Display the asked source file.
function f_source($file) {
	echo '<!DOCTYPE HTML>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<title>OpenTBS plug-in for TinyButStrong - demo source</title>
		</head>
	<body>
	'.highlight_file($file,true).'
	</body>
	</html>';
}

// Displat data in an HTML table
function f_display_result($range, $data, $options, $title) {

	echo "\n<h3>{$title}</h3>";
	
	echo "\n<p>Range name or reference : <em>{$range}</em></p>";
	if (is_array($options)) {
		echo "\n<p>Reading options: " . var_export($options, true) . "</p>";
	}
	echo "\n<p>Data:</p>";
	
	if (is_array($data)) {
		if (count($data) == 0) {
			echo "\n<p>EMPTY ARRAY</p>";
		} else {
			// Display the result
			$rec_0 = true;
			echo "\n<table border='1'>";
			$num = 0;
			foreach ($data as $k => $rec) {
				if (!is_array($rec)) {
					// For sepcial value
					$rec = array($k, $rec);
				}
				$num++;
				echo "\n<tr>";
				foreach($rec as $col => $val) {
					$txt = var_export($val, true);
					if (!is_numeric($col)) {
						$txt = $col . " = " . $txt;
					}
					echo "<td>#{$num}</td><td>" . $txt . "</td>";
				}
				echo "\n</tr>";
			}
			echo "\n</table>";
		}
	} else {
		echo "\n<p>ERROR with range '{$range}' : " . var_export($data, true) . "</p>";
	}	
}

