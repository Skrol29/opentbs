<?php

// Merge data in the body of the document
$TBS->MergeBlock('a,b', $data);

// Change chart series
$ChartNameOrNum = 'chart1';
$SeriesNameOrNum = 2;
$NewValues = array( array('Category A','Category B','Category C','Category D'), array(3, 1.1, 4.0, 3.3) );
$NewLegend = "New series 2";
$TBS->PlugIn(OPENTBS_CHART, $ChartNameOrNum, $SeriesNameOrNum, $NewValues, $NewLegend);

// Delete comments
$TBS->PlugIn(OPENTBS_DELETE_COMMENTS);

