<?php
include("db_config.php");

$challenge = $_REQUEST['hub_challenge'];
$verify_token = $_REQUEST['hub_verify_token'];

if ($verify_token === 'abc123') {
echo $challenge;
}

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
      $form_id = $details['form_id'];

      $sth = $dbh->prepare("SELECT long_token FROM tokens WHERE page_id = :page_id");
      $sth->execute(array("page_id" => $page_id));
      $access_token = $sth->fetchColumn();

      // $access_token = "EAAdWelVHOgYBAMQEqktMZBlBQptFqdmamTWYF4yZCFwRSvVnozZAJLcrzCwgPwzNR4ZBMffofh8CZCmcGzSTpeDUBMmKzzGB4Ay2y8xOHLg8sh4tKZAAB0TB7E7hfUeEiVBeIZAR34HWLLvDmLMDkhmaqxklXPZCdr2aq5fj2Jk9zMdH2nVG96wpVK6ghMmsLXp2jJq5Y3ix8AZDZD";
      $url = "https://graph.facebook.com/v2.11/{$form_id}?fields=name,qualifiers&access_token=". $access_token;

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_URL, $url);
      $res = curl_exec ($ch);
      curl_close ($ch);
      error_log($res);

      error_log(print_r($details, true));
    }
  }
}
