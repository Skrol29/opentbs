<?php

/* Build all example files
*/

$template_lst = array(
	'demo_ms_excel.xlsx',
	'demo_ms_powerpoint.pptx',
	'demo_ms_word.docx',
	'demo_oo_formula.odf' => 'simple',
	'demo_oo_graph.odg' => 'simple',
	'demo_oo_master.odm' => 'simple',
	'demo_oo_presentation.odp' => 'simple',
	'demo_oo_spreadsheet.ods',
	'demo_oo_text.odt' => 'simple',
);

$output_canevas = file_get_contents('_build_canevas.php');


foreach ($template_lst as $k => $v) {

	if (is_numeric($k)) {
		$template = $v;
		$insert_type = false;
	} else {
		$template = $k;
		$insert_type = $v;
	}
	
	$info = pathinfo($template);
	
	if ($insert_type===false) $insert_type = $info['extension'];
	$insert_file = '_build_'.$insert_type.'.php';
	
	$output_file = $info['filename'].'.php';
	
	$output_code = str_replace('%template%', $template, $output_canevas);
	
	$insert_code = file_get_contents($insert_file);
	$insert_code = str_replace('<?php', '', $insert_code);
	$insert_code = trim($insert_code);
	
	$output_code = str_replace('// %code%', $insert_code, $output_code);
	
	file_put_contents($output_file, $output_code);
	
	echo "\n<br>&bull; Template merged: $template";
	
}