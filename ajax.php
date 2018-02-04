<?php
include("db_config.php");
$app_id = "2065408253704710";
$app_secret = "e813d57562d4d5679f27d2c411310762";

$action = $_REQUEST['action'];

if ($action == "getLongLivedToken"){

  $token = $_POST['token'];
  $url = "https://graph.facebook.com/oauth/access_token?client_id={$app_id}&client_secret={$app_secret}&grant_type=fb_exchange_token&fb_exchange_token={$token}";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $output = curl_exec($ch);
  curl_close($ch);

  $array = json_decode($output, false);
  $stmt = $pdo->prepare("SELECT * FROM tokens WHERE page_id = :page_id");
  $stmt->execute(array( "page_id" => $_POST['page_id'] ));
  $result = $stmt->fetchColumn();

  if ($result) {
    $stmt = $pdo->prepare('UPDATE tokens SET long_token = :long_token WHERE page_id = :page_id');
    $stmt->execute(array(
      "long_token" => $array->access_token,
      "page_id" => $_POST['page_id']
    ));
    echo "Updated long tokens.";
  } else {
    $statement = $pdo->prepare("INSERT INTO tokens(page_id, long_token)
      VALUES(:page_id, :long_token)");
    $statement->execute(array(
        "page_id" => $_POST['page_id'],
        "long_token" => $array->access_token
    ));
    echo "New page subscribed and long tokens added.";
  }
}
