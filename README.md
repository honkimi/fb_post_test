# Facebook 投稿のサンプル

### 同時投稿の流れ

#### index.php

1. Facebook ログインの際に、scope に `publish_actions` を追加してログインしてもらう。
1. そこで得られたAccess Token と Expires をそれぞれDBに保存。

#### post.php

1. 投稿するときにまずはそのユーザーのExpiresをチェックし、もし期限が切れていたらアクセストークンを再取得
1. アクセストークンとmessage, link などを付与し、POSTすることでFacebookのタイムラインに投稿される。


### DB構成

```
mysql> show databases;
+--------------------+
| Database           |
+--------------------+
| hitch              |
+--------------------+
3 rows in set (0.01 sec)

mysql> use hitch;
Database changed

mysql> show tables;
+-----------------+
| Tables_in_hitch |
+-----------------+
| users           |
+-----------------+
1 row in set (0.00 sec)

mysql> desc users;
+--------------+--------------+------+-----+---------+----------------+
| Field        | Type         | Null | Key | Default | Extra          |
+--------------+--------------+------+-----+---------+----------------+
| id           | mediumint(9) | NO   | PRI | NULL    | auto_increment |
| fb_id        | char(30)     | NO   |     | NULL    |                |
| access_token | text         | NO   |     | NULL    |                |
| expires      | int(11)      | NO   |     | NULL    |                |
| name         | varchar(255) | YES  |     | NULL    |                |
+--------------+--------------+------+-----+---------+----------------+
5 rows in set (0.00 sec)
```


