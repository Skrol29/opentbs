<?php

// Select slide #2
$TBS->Plugin(OPENTBS_SELECT_SLIDE, 2);
// Change a picture using the command (it can also be done at the template side using parameter "ope=changepic")
$TBS->Plugin(OPENTBS_CHANGE_PICTURE, '#merge_me#', 'pic_1234f.png');

// Merge a chart
$ChartRef = 'my_chart'; // Title of the shape that embeds the chart
$SeriesNameOrNum = 1;
$NewValues = array( array('Cat. A','Cat. B','Cat. C','Cat. D'), array(0.7, 1.0, 3.2, 4.8) );
$NewLegend = "Merged";
$TBS->PlugIn(OPENTBS_CHART, $ChartRef, $SeriesNameOrNum, $NewValues, $NewLegend);



