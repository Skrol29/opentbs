<?php

// Merge data in the first sheet
$TBS->MergeBlock('a,b', $data);

// Merge cells (exending columns)
$TBS->MergeBlock('cell1,cell2', $data);

// Change the current sheet
$TBS->PlugIn(OPENTBS_SELECT_SHEET, 2);

// Merge data in Sheet 2
$TBS->MergeBlock('cell1,cell2', 'num', 3);
$TBS->MergeBlock('b2', $data);

// Delete a sheet
$TBS->PlugIn(OPENTBS_DELETE_SHEETS, 'Delete me');

// Display a sheet (make it visible)
$TBS->PlugIn(OPENTBS_DISPLAY_SHEETS, 'Display me');