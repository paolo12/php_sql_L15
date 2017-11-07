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
	if($_POST['register'] == 'Регистрация'){
		$sql_insert = $pdo->prepare('INSERT INTO `user`(`login`, `password`) VALUES (:login, :password)');
		$sql_insert->bindParam(':login', $_POST['login']);
		$sql_insert->bindParam(':password', md5($_POST['password']));
		$inserted = $sql_insert->execute();
		$_SESSION['user'] = $_POST['login'];
		header('Location: lesson_15.php');
	}
}
else{
	echo '<br>Необходимо зарегистрироваться! Заполните все поля.';
}
?>
</body>
</html>