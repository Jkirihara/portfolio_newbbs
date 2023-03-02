<?php
session_start();
require('library.php');//DB接続
//ログインチェック
$error = [];
$email = '';
$password = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
  if ($email === '' || $password === '') {
    $error['login'] = 'blank';
  } else {
    $db = dbconnect();
    $stmt = $db->prepare('select id, name, password from members where email=? limit 1');
    if (!$stmt) {
      die($db->error);
    }
    $stmt->bind_param('s', $email);
    $success = $stmt->execute();
    if (!$success) {
      die($db->error);
    }
    $stmt->bind_result($id, $name, $hash);
    $stmt->fetch();
    if (password_verify($password, $hash)) {
    //ログイン成功
      session_regenerate_id();
      $_SESSION['id'] = $id;
      $_SESSION['name'] = $name;
      header('Location: index.php');
      exit();
    } else {
      $error['login'] = 'failed';
    }
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="shortcut icon" href="img/man.jpeg">
<title>ログインページ</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="outline">
  <header class="header"><h1>CommunicationApplication&lt;ログインページ&gt;</h1></header>
  <div class="content">
    <div id="information">
      <h2 id="guidance">メールアドレスとパスワードを入力してログインしてください。</h2>
      <div id="register">
        <p class="caution">&#042;ユーザー登録がまだの方はこちらから</p>
        <div><a href="join/"id="userregistration">ユーザー登録する</a></div>
      </div>
    </div>
    <form action="" method="post">
      <dl>
        <dt>メールアドレス</dt>
        <dd>
          <input type="text" name="email" size="35" maxlength="255" value="<?php echo h($email); ?>" />
            <?php if (isset($error['login']) && $error['login'] === 'blank'): ?>
            <p class="caution">&#042;メールアドレスとパスワードをご記入ください。</p>
            <?php endif; ?>
            <?php if (isset($error['login']) && $error['login'] === 'failed'): ?>
            <p class="caution">&#042;ログインに失敗しました。正しくご記入ください。</p>
            <?php endif; ?>
        </dd>
        <dt>パスワード</dt>
        <dd><input type="password" name="password" size="35" maxlength="255" value="<?php echo h($password); ?>" /></dd>
      </dl>
      <div>
      <input type="submit" value="ログイン" class="btn">
      </div>
    </form>
  </div>
  <footer id="footer"><p id="year">&copy;<?php echo date('Y'); ?></p></footer>
</div>
</body>
</html>