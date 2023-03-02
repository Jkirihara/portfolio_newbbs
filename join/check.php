<?php
session_start();
require('../library.php');//DB接続

if (isset($_SESSION['form'])) {
	$form = $_SESSION['form'];
} else {
	header('Location: index.php');
	exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$db = dbconnect();
	$stmt = $db->prepare('insert into members (name, email, password, picture) VALUES (?, ?, ?, ?)');
	if (!$stmt) {
		die($db->error);
	}
	$password = password_hash($form['password'], PASSWORD_DEFAULT);
	$stmt->bind_param('ssss', $form['name'], $form['email'], $password, $form['image']);
	$success = $stmt->execute();
	if (!$success) {
		die($db->error);
	}
	unset($_SESSION['form']);
	header('Location: thanks.php');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="shortcut icon" href="../img/man.jpeg">
<title>登録情報確認</title>
<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div class="outline">
	<header class="header"><h1>CommunicationApplication&lt;登録情報確認&gt;</h1></header>
	<div class="content">
		<p class="caution">&#042;記入した内容を確認して、「登録する」ボタンをクリックしてください。</p>
			<form action="" method="post">
				<dl>
					<dt>氏名</dt>
						<dd><?php echo h($form['name']); ?></dd>
					<dt>メールアドレス</dt>
						<dd><?php echo h($form['email']); ?></dd>
					<dt>パスワード</dt>
						<dd>&#091;パスワード保護のため、表示できません。&#093;</dd>
					<dt>アイコン登録</dt>
						<dd>
							<?php if ($form['image']) : ?>
								<img src="../member_picture/<?php echo h($form['image']); ?>" width="100" alt="" />
							<?php endif; ?>
						</dd>
				</dl>
				<div>
					<a href="index.php?action=rewrite"class="return">&laquo;記入内容を変更する</a>| 
				    <input type="submit" value="登録する" class="btn"/>
				</div>
			</form>
	</div>
	<footer id="footer"><p id="year">&copy;<?php echo date('Y'); ?></p></footer>
</div>
</body>
</html>