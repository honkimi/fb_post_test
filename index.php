<?php
  $link = mysql_connect('localhost', 'root', '');
  if (!$link) {
    print(mysql_error());
  }

  $db_selected = mysql_select_db('hitch', $link);
  if (!$db_selected){
    die('Failed to connect the db.'.mysql_error());
  }


session_start();
$client_id = '...';
$client_secret = '...';
$redirect_uri = 'http://localhost:8888/';

$code = $_REQUEST['code'];
$error = $_REQUEST['error'];
$error_reason = $_REQUEST['error_reason'];
$error_description = $_REQUEST['error_description'];

if (empty($code) && empty($error)) {
    header('Location: https://graph.facebook.com/oauth/authorize'
                   . '?client_id=' . $client_id
                   . '&redirect_uri=' . urlencode($redirect_uri)
                   . '&scope=publish_actions'
                   . '&display=popup');
                   
} else {
    if (!empty($code)) {
      $token_url = 'https://graph.facebook.com/oauth/access_token' 
                       . '?client_id=' . $client_id
                       . '&redirect_uri=' . urlencode($redirect_uri) 
                       . '&client_secret=' . $client_secret 
                       . '&code=' . $code;
	    $res = file_get_contents($token_url);
      parse_str($res);
	    //save db

      $me_url = 'https://graph.facebook.com/me?access_token=' . $access_token;
      $me_res = file_get_contents($me_url);
      $me_json = json_decode($me_res);
      $fb_id = $me_json->{"id"};
      $name = $me_json->{"name"};
      $expires_val = time() + $expires;
      // save fb data
      $result = mysql_query("INSERT INTO users (fb_id, access_token, expires, name) VALUES ('$fb_id', '$access_token', '$expires_val' ,'$name')", $link);
      if (!$result) {
        exit('failed to registered.');
      }

      echo ('Registration finished!' . $name);
    } else if (!empty($error)) {
        echo 'error:' . $error 
          . '/error_reason:' . $error_reason 
          . '/error_description:' . $error_description;
    }
}

  mysql_close($link);

?>


