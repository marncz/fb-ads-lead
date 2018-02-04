<?php
include("db_config.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $challenge = $_REQUEST['hub_challenge'];
// $verify_token = $_REQUEST['hub_verify_token'];

// if ($verify_token === 'abc123') {
//   echo $challenge;
// }

//echo $res;
error_log(file_get_contents('php://input'));
$input = json_decode(file_get_contents('php://input'), true);
error_log(print_r($input, true));

// Get lead information.
//print_r($input);

foreach ($input['entry'] as $entry) {
  $form_id = $entry['id'];
  foreach ($entry['changes'] as $change) {
    if($change['field'] == "leadgen"){
      $details = $change['value'];
      $page_id = $details['page_id'];
      $leadgen_id = $details['leadgen_id'];
      $form_id = $details['form_id'];

      $sth = $pdo->prepare("SELECT long_token FROM tokens WHERE page_id = :page_id");
      $sth->execute(array("page_id" => $page_id));
      $access_token = $sth->fetchColumn();

      $url = "https://graph.facebook.com/v2.11/{$leadgen_id}?access_token=". $access_token;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $res = curl_exec ($ch);
      curl_close ($ch);
      $data = json_decode($res, true);

      // Get form details e.g. name
      $url = "https://graph.facebook.com/v2.11/{$form_id}?access_token=". $access_token;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $response = curl_exec ($ch);
      curl_close ($ch);
      $form_name = json_decode($response, true)['name'];

      $fields = array();

      foreach($data['field_data'] as $field){
        $fields[$field['name']] = $field['values'][0];
      }

      $statement = $pdo->prepare("INSERT INTO lead_data(id, page_id, form_name, first_name, last_name, phone, email)
        VALUES(:id, :page_id, :form_name, :first_name, :last_name, :phone, :email)");
      $statement->execute(array(
        "id" => $data['id'],
        "page_id" => $page_id,
        "form_name" => $form_name,
        "first_name" => (array_key_exists("first_name", $fields)) ? $fields['first_name'] : "",
        "last_name" => (array_key_exists("last_name", $fields)) ? $fields['last_name'] : "",
        "email" => (array_key_exists("email", $fields)) ? $fields['email'] : "",
        "phone" => (array_key_exists("phone_number", $fields)) ? $fields['phone_number'] : ""
      ));

      print_r($fields);

    }
  }
}
