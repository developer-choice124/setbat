<?php

$q_create = array(
    array("field" => "name", "label" => "Question", "rules" => "required")
);
$qcat_create = array(
    array("field" => "name", "label" => "Question", "rules" => "required")
);
$a_create = array(
    array("field" => "name", "label" => "Answer", "rules" => "required"),
    array("field" => "is_correct", "label" => "Correct answer", "rules" => "required"),
);
$testpaper_create = array(
    array("field" => "title", "label" => "Title", "rules" => "required"),
    array("field" => "qcount", "label" => "Question count", "rules" => "required|numeric"),
    array("field" => "details", "label" => "Details", "rules" => "required"),
    array("field" => "dt_from", "label" => "Date from ", "rules" => "required"),
    array("field" => "dt_to", "label" => "Date to", "rules" => "required"),
    array("field" => "qcat_id", "label" => "Date to", "rules" => "required"), 
);
$config['q_create'] = $q_create;
$config['a_create'] = $a_create;
$config['qcat_create'] = $qcat_create;
$config['testpaper_create'] = $testpaper_create;
