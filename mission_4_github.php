<?php
//DB接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
try{
	$pdo = new PDO($dsn, $user, $password);
}catch(PDOException $e){
	echo 'データベースにアクセス出来ません．'.$e->getMeaasge();
	exit;
}

//DB内にテーブル作成
$sql = "CREATE TABLE mission_4"
."("
."id INT,"
."name char(32),"
."comment TEXT,"
."time char(32),"
."pass TEXT"
.");";
$stmt = $pdo->query($sql);

//作成したテーブルの確認
$sql = 'SHOW TABLES';
$table = $pdo->query($sql);
foreach($table as $row){
	echo $row[0];
	echo '<br>';
}
echo "<hr>";

//テーブルの中身を確認
$sql = 'SHOW CREATE TABLE mission_4';
$contents = $pdo->query($sql);
foreach($contents as $row){
	print_r($row);
}
echo "<hr>";

if(!empty($table)){
	if ((empty($_POST['edit_id'])) && (!empty($_POST['name'])) && (!empty($_POST['comment'])) && (!empty($_POST['pass']))) { //投稿機能
		//DB内のテーブルにデータを入力
		$sql = $pdo->prepare("INSERT INTO mission_4(id, name, comment, time, pass) VALUES(:id, :name, :comment, :time, :pass)");
		$sql->bindParam(':id', $id, PDO::PARAM_STR);
		$sql->bindParam(':name', $name, PDO::PARAM_STR);
		$sql->bindParam(':comment', $comment, PDO::PARAM_STR);
		$sql->bindParam(':time', $time, PDO::PARAM_STR);
		$sql->bindParam(':pass', $pass, PDO::PARAM_STR);
		//投稿番号取得
		$sql_id = 'SELECT id FROM mission_4';
		$results = $pdo->query($sql_id);
		$id = 1;
		foreach($results as $row){
			$id += 1;
		}
		$name = $_POST['name'];
		$comment = $_POST['comment'];
		$time = date("Y年m月d日 H時i分s秒");
		$pass = $_POST['pass'];
		$sql->execute();
	}else if((!empty($_POST['delete'])) && (!empty($_POST['d_pass']))){ //削除機能
		$sql = $pdo->prepare("SELECT * FROM mission_4 where id=:id ORDER BY id");
		$sql->bindParam(':id', $id, PDO::PARAM_STR);
		$id = $_POST['delete'];
		$sql->execute();
		$results = $sql->fetch(PDO::FETCH_ASSOC);
		$sql->closeCursor();
		$pw = $_POST['d_pass'];
		$d_flag = 0;
		if($results['pass'] == $pw){
			$d_flag = 1;
		}else{
			echo 'パスワードが間違っています．' . "<br>";
		}
		if($d_flag == 1){
			$sql = "DELETE FROM mission_4 WHERE id=$id";
			$result = $pdo->query($sql);
			//投稿番号再振り分け
			$sql = 'SELECT * FROM mission_4 ORDER BY id';
			$results = $pdo->query($sql);
			foreach($results as $row){
				if($row['id'] >$id){
					$r_id = $row['id'] - 1;
					$seq = $row['id'];
					$sql = "UPDATE mission_4 SET id='$r_id' WHERE id=$seq";
					$result = $pdo->query($sql);
				}
			}
		}
	}else if((!empty($_POST['edit'])) && (!empty($_POST['e_pass']))){ //編集機能（対象指定）
		$sql = $pdo->prepare("SELECT * FROM mission_4 where id=:id ORDER BY id");
		$sql->bindParam(':id', $id, PDO::PARAM_STR);
		$id = $_POST['edit'];
		$sql->execute();
		$results = $sql->fetch(PDO::FETCH_ASSOC);
		$sql->closeCursor();
		$pw = $_POST['e_pass'];
		$e_flag = 0;
		if($results['pass'] == $pw){
			$e_flag = 1;
		}else{
			echo 'パスワードが間違っています．' . "<br>";
		}
		if($e_flag == 1){
			$e_name = $results['name'];
			$e_comment = $results['comment'];
			$e_id = $results['id'];
			$e_flag = 0;
		}
		
	}else if((!empty($_POST['edit_id'])) && (!empty($_POST['name'])) && (!empty($_POST['comment']))){ //編集機能（編集実行）
		$id = $_POST['edit_id'];
		$name = $_POST['name'];
		$comment = $_POST['comment'];
		$time = date("Y年m月d日 H時i分s秒");
		$sql = "UPDATE mission_4 SET name='$name', comment='$comment', time='$time' WHERE id=$id";
		$result = $pdo->query($sql);
	}
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>mission_4</title>
</head>
<body>
<form action="mission_4.php" method="post">
名前：<input type="text" name="name" value="<?php if(!empty($e_name)){echo $e_name;}else{echo "";} ?>"/><br>
コメント：<input type="text" name="comment" value="<?php if(!empty($e_comment)){echo $e_comment;}else{echo "";} ?>"/><br>
パスワード：<input type="text" name="pass" />
<input type="hidden" name="edit_id" value="<?php if(!empty($e_id)){echo $e_id;}else{echo "";} ?>"/>
<input type="submit" value="送信" /><br><br>
削除対象番号：<input type="number" name="delete" /><br>
パスワード：<input type="text" name="d_pass" />
<input type="submit" value="送信" /><br><br>
編集対象番号：<input type="text" name="edit" /><br>
パスワード：<input type="text" name="e_pass" />
<input type="submit" value="送信" /><br><br>
</form>

<?php
if(!empty($table)){
	//DB内のデータを表示
	$sql = 'SELECT * FROM mission_4 ORDER BY id ASC';
	$results = $pdo->query($sql);
	if(!empty($results)){
		foreach($results as $row){ 
			//rowの中にはテーブルのカラム名が入る
			echo $row['id'].',';
			echo $row['name'].',';
			echo $row['comment'].',';
			echo $row['time'].',';
			echo $row['pass'].'<br>';
		}
	}
}
?>
</body>
</html>
