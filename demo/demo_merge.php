<?php

// Display the asked source file.
function f_source($file) {
	echo '<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>OpenTBS plug-in for TinyButStrong - demo source</title></head><body>'.highlight_file($file,true).'</body></html>';
}


// Retrieve the template to open
$template = (isset($_POST['tpl'])) ? $_POST['tpl'] : '';
$template = basename($template); // for security
$info= pathinfo($template);
$script = $info['filename'].'.php';

// Checks
if (substr($template,0,5)!=='demo_') exit("Wrong file.");
if (!file_exists($template)) exit("The asked template does not exist.");


if (isset($_POST['btn_template'])) {
	header('Location: '.$template); 
	exit;
}

if (isset($_POST['btn_script'])) {
	f_source($script);
	exit;
}

// Start the demo
if (isset($_POST['btn_result'])) {
	include($script);
	exit;
}
