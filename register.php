<?php
$pdo = new PDO('mysql:host=localhost;dbname=global;charset=UTF8', 'root', 'qwerty');
?>
<html>
<head>
</head>
<body>
<p>Введите данные для регистрации или войдите, если уже регистрировались:</p>
<form method="POST">
    <input type="text" name="login" placeholder="Логин">
    <input type="password" name="password" placeholder="Пароль">
    <input type="submit" name="sign_in" value="Вход">
    <input type="submit" name="register" value="Регистрация">
</form>

<?php
if(!empty($_POST['login'])){	
	$sql_select_user = $pdo->prepare('SELECT login FROM user');
	$sql_select_user->execute();
	$users_login = $sql_select_user->fetchAll(PDO::FETCH_COLUMN);
	
	if($_POST['register'] == 'Регистрация'){
		$login = strval($_POST['login']);
		
		if (in_array($_POST['login'], $users_login)) {
			echo "Такой логин уже есть в базе! Введит логин и пароль и нажмите 'Вход'.";
		}
		else{
			$sql_insert = $pdo->prepare('INSERT INTO user(login, password) VALUES (:login, :password)');
			$sql_insert->bindParam(':login', $_POST['login']);
			$sql_insert->bindParam(':password', md5($_POST['password']));
			$inserted = $sql_insert->execute();
			$_SESSION['user'] = $_POST['login'];
			header('Location: lesson_15.php');
		}
	}
}
else{
	echo '<br>Необходимо зарегистрироваться! Заполните все поля.';
}

if(!empty($_POST['sign_in'])){
	if($_POST['sign_in'] == 'Вход'){
		$sql_select_user = $pdo->prepare('SELECT * FROM user WHERE login = :user_login LIMIT 1');
		$sql_select_user->bindParam(':user_login', $_POST['login']);
		$sql_select_user->execute();
		$user_db_pass = $sql_select_user->fetchAll(PDO::FETCH_COLUMN, 2);

		$user_password = md5($_POST['password']);

		if($user_db_pass == $user_password){
			header('Location: lesson_15.php');
		}
		else{
			header('Location: register.php');
		}
	}
}
?>
</body>
</html>