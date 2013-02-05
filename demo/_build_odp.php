<?php

// Merge data in a table (there is no need to select the slide with an ODP)
$TBS->MergeBlock('b', $data);

// Hide a slide
$TBS->PlugIn(OPENTBS_DISPLAY_SLIDES, 'slide to hide', false);

// Delete a slide
$TBS->PlugIn(OPENTBS_DELETE_SLIDES, 'slide to delete');

