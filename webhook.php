<?php

$challenge = $_REQUEST['hub_challenge'];
$verify_token = $_REQUEST['hub_verify_token'];

if ($verify_token === 'abc123') {
echo $challenge;
}

$input = json_decode(file_get_contents('php://input'), true);
error_log(print_r($input, true));

// Get lead information.
print_r($input);

foreach ($input['entry'] as $entry) {
  foreach ($entry['changes'] as $change) {
    if($change['field'] == "leadgen"){
      $details = $change['value'];
      $page_id = $details['page_id'];
      $leadgen_id = $details['leadgen_id'];

      error_log(print_r($details, true));
    }
  }
}
