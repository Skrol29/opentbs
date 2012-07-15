<?php

// Merge data in the Workbook (all sheets)
$TBS->MergeBlock('a,b', $data);

// Merge data in Sheet 2
// No need to change the current sheet, they are all stored in the same XML subfile.
$TBS->MergeBlock('cell1,cell2', 'num', 3);
$TBS->MergeBlock('b2', $data);

// Delete a sheet
$TBS->PlugIn(OPENTBS_DELETE_SHEETS, 'Delete me');

// Display a sheet (make it visible)
$TBS->PlugIn(OPENTBS_DISPLAY_SHEETS, 'Display me');