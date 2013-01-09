<?php

// Merge data in the first slide of the presentation
$TBS->MergeBlock('a,b', $data);

// Select slide #2
$TBS->Plugin(OPENTBS_SELECT_SLIDE, 2);

// Change a pitcure using the command (it can also be done at the template side using parameter "ope=changepic")
$TBS->Plugin(OPENTBS_CHANGE_PICTURE, '#merge_me#', 'pic_1234f.png');
