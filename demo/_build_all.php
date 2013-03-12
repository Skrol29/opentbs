<?php

/* Build all example files
*/

$template_lst = array(
	'demo_ms_excel.xlsx',
	'demo_ms_powerpoint.pptx',
	'demo_ms_word.docx',
	'demo_oo_formula.odf',
	'demo_oo_graph.odg',
	'demo_oo_presentation.odp',
	'demo_oo_spreadsheet.ods',
	'demo_oo_text.odt',
);

$output_canevas = file_get_contents('_build_canevas.php');


foreach ($template_lst as $template) {

	$info = pathinfo($template);
	
	$insert_type = $info['extension'];
	$insert_file = '_build_'.$insert_type.'.php';
	
	if (file_exists($insert_file)) {
		$default = false;
	} else {
		$default = true;
		$insert_file = '_build_default.php';
	}
	
	$output_file = $info['filename'].'.php';
	
	$output_code = str_replace('%template%', $template, $output_canevas);
	
	$insert_code = file_get_contents($insert_file);
	$insert_code = str_replace('<?php', '', $insert_code);
	$insert_code = trim($insert_code);
	
	$output_code = str_replace('// %code%', $insert_code, $output_code);
	
	file_put_contents($output_file, $output_code);
	
	echo "\n<br>&bull; Template merged: $template";
	if ($default) echo " (default canevas)";
	
}