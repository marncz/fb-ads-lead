<?php

$challenge = $_REQUEST['hub_challenge'];
$verify_token = $_REQUEST['hub_verify_token'];

if ($verify_token === 'abc123') {
echo $challenge;
}

$leadgen_id = "2134001436835979";
$url = "https://graph.facebook.com/v2.11/2134001436835979";


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "access_token=EAALMCEKIYPkBAAloNQPvJchK08ojZBDXNg2wH0MAu3utdwraZBvrY4g8OCJPlUPCyGfW8tUkUZBGRZCAXorpvVsJqSddVIzRn5XUx5aUghnYLYZCZA2JOMvb5CSasYMaQj2ccw1pQyHmzezpimotc16SfZCxmvDI6oUAwils2e1alRqacKRZAV4IlqKZCsAx1dxWMKcPcrHCAjgZDZD");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec ($ch);
curl_close ($ch);

//echo $res;

$input = json_decode(file_get_contents('php://input'), true);
error_log(print_r($input, true));

// Get lead information.
//print_r($input);

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
