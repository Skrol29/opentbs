<?php

// Merge data in the body of the document
$TBS->MergeBlock('a,b', $data);

// Merge data in colmuns
$data = array(
 array('date' => '2013-10-13', 'thin' => 156, 'heavy' => 128, 'total' => 284),
 array('date' => '2013-10-14', 'thin' => 233, 'heavy' =>  25, 'total' => 284),
 array('date' => '2013-10-15', 'thin' => 110, 'heavy' => 412, 'total' => 130),
 array('date' => '2013-10-16', 'thin' => 258, 'heavy' => 522, 'total' => 258),
);
$TBS->MergeBlock('c', $data);


// Change chart series
$ChartNameOrNum = 'a nice chart'; // Title of the shape that embeds the chart
$SeriesNameOrNum = 'Series 2';
$NewValues = array( array('Category A','Category B','Category C','Category D'), array(3, 1.1, 4.0, 3.3) );
$NewLegend = "Updated series 2";
$TBS->PlugIn(OPENTBS_CHART, $ChartNameOrNum, $SeriesNameOrNum, $NewValues, $NewLegend);

// Delete comments
$TBS->PlugIn(OPENTBS_DELETE_COMMENTS);

