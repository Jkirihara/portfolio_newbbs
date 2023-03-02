<?php
session_start();//DB接続
require('library.php');

if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
	$id = $_SESSION['id'];
	$name = $_SESSION['name'];
} else {
	header('Location: login.php');
	exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
  header('Location: index.php');
  exit();
}

$db = dbconnect();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="shortcut icon" href="img/man.jpeg">
<title>全文表示</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="outline">
	<header class="header"><div class="headerdetail"><h1>CommunicationApplication</h1><div id="datetime"></div></div></header>
	<div class="content">
		<p><a href="index.php"class="return">&laquo;コミュニケーションページへ戻る</a></p>
		<?php
		$stmt = $db->prepare('select p.id, p.member_id, p.message, p.modified, m.name, m.picture from posts p, 
							members m where p.id=? and m.id=p.member_id order by id desc');
		if (!$stmt) {
			die($db->error);
		}
		$stmt->bind_param('i', $id);
		$success = $stmt->execute();
		if (!$success) {
			die($db->error);
		}
		$stmt->bind_result($id, $member_id, $message, $modified, $name, $picture);
		if ($stmt->fetch()):
		?>
		<div>
			<?php if ($picture): ?>
				<img src="member_picture/<?php echo h($picture); ?>" width="48" height="48" alt="" />
			<?php endif; ?>
				<p id="postname">投稿者:<?php echo h($name);?></p>
				<p id="posttime">投稿時間:<?php echo h($modified); ?></p>
				<p><?php echo h($message); ?></p>
			<?php if ($_SESSION['id'] === $member_id): ?>
				<a href="delete.php?id=<?php echo h($id); ?>" style="color: #F33;">&#91;削除&#93;</a>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<p>その投稿は削除されたか、URLが間違えています。</p>
		<?php endif; ?>
	</div>
	<footer id="footer"><p id="year">&copy;<?php echo date('Y'); ?></p></footer>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="./main.js"></script>
</body>
</html>