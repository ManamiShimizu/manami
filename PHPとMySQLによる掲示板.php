<?php

//データベースへ接続
	$dsn = 'データベース名';//データベース名、ホスト名
	$user = 'ユーザー名';//ユーザー名
	$password = 'パスワード';//パスワード
	$pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//データベース内にテーブルを作成
	$sql = "CREATE TABLE IF NOT EXISTS manamidb"//IF NOT EXISTSで警告防止
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY," //投稿番号
	. "name char(30)," //名前
	. "comment char(30)," //コメント
	. "date char(30)," //投稿日時
	. "pass char(30)" //パスワード
	.");";
	$stmt = $pdo->query($sql);

if (!empty ($_POST["send"])){//送信ボタンが押された場合
	if (!empty ($_POST["name"] and $_POST["comment"] and $_POST["password1"] and $_POST["hidden"])){ //名前、コメント、パスワード1、hiddenに値が入っている場合
	
		//以下updeteで編集開始
		
		$id = $_POST["hidden"]; //投稿番号
		$name = $_POST["name"]; //名前
		$comment = $_POST["comment"]; //コメント
		$date = date ("Y年m月d日 H:i:s");  //日時
		$pass = $_POST["password1"]; //パスワード
		
		$sql = 'update manamidb set name=:name,comment=:comment,date=:date,pass=:pass where id=:id'; 
		$stmt = $pdo->prepare($sql); //sql準備
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		//編集終了

	}elseif (!empty ($_POST["name"] and $_POST["comment"] and $_POST["password1"])){//名前、コメント、パスワード1に値が入っている場合
	
		//insertで新規投稿開始

		$name = $_POST["name"];
		$comment = $_POST["comment"];
		$date = date ("Y年m月d日 H:i:s");
		$pass = $_POST["password1"];
		$sql = $pdo -> prepare("INSERT INTO manamidb (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
		$sql -> bindParam(':name', $name, PDO::PARAM_STR);
		$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
		$sql -> bindParam(':date', $date, PDO::PARAM_STR);
		$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
		$sql -> execute();
		
		//新規投稿終了
	}
}

if (!empty($_POST["send_delete"])){//削除ボタンが押された場合
	if (!empty($_POST["delete"] and $_POST["password2"])){//削除対象番号とパスワード2に値が入っている場合
	
		//selectでデータを確認
		$sql = 'SELECT * FROM manamidb';
		$stmt = $pdo->query($sql);
		$result = $stmt->fetchAll();

		foreach ($result as $row){//ループ開始
		
			if ($row['pass'] == $_POST["password2"]){//パスワードが一致していた場合

				$id = $_POST["delete"];
				$sql = 'delete from manamidb where id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();
			}
		}//ループ終了
	}
}

if (!empty($_POST["send_edit"])){//編集ボタンが押された場合
	if (!empty($_POST["edit"] and $_POST["password3"])){	//編集対象番号とパスワード3が入力されている場合

		//selectでデータを確認
		$sql = 'SELECT * FROM manamidb';
		$stmt = $pdo->query($sql);
		$result = $stmt->fetchAll();

		foreach ($result as $row){//ループ開始
			if ($row['pass'] == $_POST["password3"] || $row['id'] == $_POST["edit"]){	//パスワードが一致し、投稿番号と編集対象番号が一致した場合
				$editname = $row['name'];//名前フォーム内表示用
				$editcomment = $row['comment'];//コメントフォーム内表示用
				$editnum = $_POST["edit"];
			}
		}//ループ終了
	}
}
?>

<html>
    <font size="5">掲示板</font><br>
	<hr>

	<form method = "post" action = "mission_5-1.php">


	名前<br>
 	<input type = "text" name = "name" size = "30"
	value = "<?php if (!empty($_POST["send_edit"])){if(!empty($_POST["edit"] and $_POST["password3"]) && $_POST["password3"]==$row['pass']){echo $editname;}}?>" ><br>

	<?php
	if (!empty ($_POST["send"])){ 
		if (empty ($_POST["name"])){ 
			echo "<font color = \"red\"> 名前を入力してください。</font><br>";
		}
	}
	?> 
	

	コメント<br>
	 <input type = "text" name = "comment" size = "30" 
	 value = "<?php if (!empty($_POST["send_edit"])){if(!empty($_POST["edit"] and $_POST["password3"]) && $_POST["password3"]==$row['pass']){echo $editcomment;}}?>" ><br>

	<?php
	if (!empty ($_POST["send"])){ 
		if (empty ($_POST["comment"])){ 
			echo "<font color =\"red\"> コメントを入力してください。 </font><br>";
		}
	}
	?> 
	

	パスワード<br>
	<input type = "text" name = "password1" size = "10" value = ""><br>
	
	<?php
	if (!empty ($_POST["send"])){
		if (empty ($_POST["password1"])){echo "<font color = \"red\"> パスワードを入力してください。 </font><br>";
		}
	}
	?>

	<input type = "submit" name = "send" value = "送信"><br><br>
	
	<input type = "hidden" name = "hidden" size = "30" value = "<?php if(!empty($_POST["edit"])){echo $editnum;}?>">


	削除対象番号<br>
	 <input type = "text" name = "delete" size = "10"value = "" ><br>

	 <?php
	if (!empty ($_POST["send_delete"])){ 
		if (empty ($_POST["delete"])){ 
			echo "<font color = \"red\"> 番号を入力してください。 </font><br>";
		}
	}
	?> 


	パスワード<br>
	<input type = "text" name = "password2" size = "10" value = ""><br>
	
	<?php
	if (!empty ($_POST["send_delete"])){
		if (empty ($_POST["password2"])){
			echo "<font color = \"red\"> パスワードを入力してください。 </font><br>";
		}elseif (!empty ($_POST["password2"] and $_POST["delete"]) && $_POST["password2"] !== $row['pass']){
			echo "<font color = \"red\"> パスワードが違います。</font><br>";
		}
	}
	?>
		
	<input type = "submit" name = "send_delete" value = "削除"><br><br>
	

	編集対象番号<br>
 	<input type = "text" name = "edit" size = "10" value = "" ><br>

	 <?php
	if (!empty ($_POST["send_edit"])){ 
		if (empty ($_POST["edit"])){ 
			echo "<font color = \"red\"> 番号を入力してください。 </font><br>";
		}
	}
	?> 
	

	パスワード<br>
	<input type = "text" name = "password3" size = "10" value = ""><br>

	<?php 
	if (!empty ($_POST["send_edit"])){
		if (empty ($_POST["password3"])){
			echo "<font color = \"red\"> パスワードを入力してください。 </font><br>";
		}elseif(!empty ($_POST["password3"] and $_POST["edit"]) && $_POST["password3"] !== $row['pass']){
			echo "<font color = \"red\"> パスワードが違います。 </font><br>";
		}
	}
	?>
	
	<input type = "submit" name = "send_edit" value = "編集">
	<hr>

	</form>
</html>

	<?php
	
	//以下selectで表示機能

	$sql = 'SELECT * FROM manamidb';
	$stmt = $pdo->query($sql);
	$result = $stmt->fetchAll();

	foreach ($result as $row){
	echo $row['id']." ".$row['name']." ".$row['date']." "."<br>";
	echo $row['comment'];
	echo "<hr>";
	}

	?>
