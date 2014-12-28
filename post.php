<?php
  $client_id = '...';
  $client_secret = '...';

  $link = mysql_connect('localhost', 'root', '');
  if (!$link) {
    print(mysql_error());
  }

  $db_selected = mysql_select_db('hitch', $link);
  if (!$db_selected){
    die('Failed to connect the db.'.mysql_error());
  }


  if($_SERVER["REQUEST_METHOD"] != "POST"){
  //get
    $result = mysql_query('SELECT id, name from users');
  } else {
  //post
    $result = mysql_query('SELECT access_token, expires from users where id=' .  $_POST['id'], $link);
    $row = mysql_fetch_assoc($result);

    $token = "";
    if (time() > intval($row['expires'])) {
      //refresh access token
      $token_url = 'https://graph.facebook.com/oauth/access_token' 
                       . '?client_id=' . $client_id
                       . '&client_secret=' . $client_secret 
                       . '&grant_type=fb_exchange_token'
                       . '&fb_exchange_token=' . $row['access_token'];
	    $res = file_get_contents($token_url);
      parse_str($res);
      $result = mysql_query('UPDATE users set access_token="' . $access_token 
        . '", expires="' . (time() + $expires) . '" where id=' .  $_POST['id'], $link);
      $token = $access_token;
    } else {
      $token = $row['access_token'];
    }
    $publish_url = "https://graph.facebook.com/me/feed";
    $data = array(
        'access_token' => $token,
        'message' => $_POST['message'],
        'link' => 'http://hitchme.jp/',
    );
    $options = array('http' => array(
        'method' => 'POST',
        'content' => http_build_query($data),
    ));
    $result = file_get_contents($publish_url, false, stream_context_create($options));
    if ($result) {
      echo('Yay! Successfully posted!');
    }
  }
?>
<html>
<head>
  <title>post test</title>
</head>
<body>
  <form method="post" action="/post.php">
    <?php while ($row = mysql_fetch_assoc($result)) { ?>
      <label><input type="radio" name="id" value="<?php echo($row['id']); ?>" /><?php echo($row['name']); ?></label><br />
    <?php } ?>
    <br />
    <label>Content: <input type="text" name="message" value="<?php echo('あ'); ?>" /></label><br /> <br />
    <input type="submit" value="post to facebook" />
  </form>
</body>
</html>
  
<?php
  mysql_close($link);
?>
