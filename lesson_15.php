<?php
$pdo = new PDO('mysql:host=localhost;dbname=global;charset=UTF8', 'root', 'qwerty');

date_default_timezone_set('Europe/Moscow');
$time = date("Y-m-d H:m:s");

/*
Функция отрисовывает таблицу
*/
function getTable($row){
	if(empty($row)){
		return '<tr><td></td><td></td><td id="center"></td><td></td><td></td><td></td><td></td></tr>';
	}
	else{
		if($row['is_done'] == 0){
			$task_status = '<span style="color: orange;">В процессе</span>';
		}
		else{
			$task_status = '<span style="color: green;">Выполнено</span>';
		}
		
		return '<tr>'.'<td>'.$row['description'].'</td>'.'<td>'.$row['date_added'].'</td>'.'<td id="center">'.$task_status.'</td>'.'<td>'.'<a href=?id='.$row['id'].'&action=edit>Изменить</a>'.' '.'<a href=?id='.$row['id'].'&action=done>Выполнить</a>'.' '.'<a href=?id='.$row['id'].'&action=delete>Удалить</a>'.'</td>'.'<td>'.assignUser($row['id'], $row['assigned_user_id']).'</td>'.'<td>'.getUserName($row['user_id']).'</td>'.'<td><form method="GET"><select name="assigned_user">'.getListUsers().'</select><input type="submit" name="assign" value="Назначить" /></form></td>'.'</tr>';
	}
}

/*
Функция отображает логин пользователя
*/
function getUserName($row_user_id){
	$pdo = new PDO('mysql:host=localhost;dbname=global;charset=UTF8', 'root', 'qwerty');
	
	$sql_select_user = $pdo->prepare('SELECT id FROM user WHERE login = :user');
	$sql_select_user->bindParam(':user', $_SESSION['user']);
	$sql_select_user->execute();
	$user_id = $sql_select_user->fetchColumn();
	
	if($user_id == $row_user_id){
		return 'Вы';
	}
	else{
		$sql_select_login = $pdo->prepare('SELECT login FROM user WHERE id = :user_id');
		$sql_select_login->bindParam(':user_id', $row_user_id);
		$sql_select_login->execute();
		$user_login = $sql_select_login->fetchColumn();
		return $user_login;
	}
}

/*
Функция отрисовывает выпадающий список пользователей
*/
function getListUsers(){
	$pdo = new PDO('mysql:host=localhost;dbname=global;charset=UTF8', 'root', 'qwerty');
	$sql_list_users = $pdo->prepare('SELECT * FROM user');
	$sql_list_users->execute();
	$data = $sql_list_users->fetchAll();
	$list_str ='';
	
	foreach ($data as $row) {
		$list_str = $list_str.'<option selected value="'.$row["login"].'">'.$row["login"].'</option>';
	}
	
	return $list_str;
}

/*
Функция назначает пользователя
*/
function assignUser($row_id, $row_assigned_user_id){
	if(empty($_GET['assigned_user'])){
		return getUserName($row_assigned_user_id);
	}
	else{
		$pdo = new PDO('mysql:host=localhost;dbname=global;charset=UTF8', 'root', 'qwerty');
		
		$sql_user_id = $pdo->prepare('SELECT id FROM user WHERE login = :user_login');
		$sql_user_id->bindParam(':user_login', $_GET['assigned_user']);
		$sql_user_id->execute();
		$user_id = $sql_user_id->fetchColumn();
		
		$sql = 'UPDATE task SET assigned_user_id = :assigned_user_id WHERE id= :row_id';
		$row_id = $row_id;
		$assigned_user_id = $user_id;
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':row_id', $row_id);
		$stmt->bindParam(':assigned_user_id', $assigned_user_id, PDO::PARAM_INT);	
		$stmt->execute();
		
		$sql_select_login = $pdo->prepare('SELECT assigned_user_id FROM task WHERE id = :task_id');
		$sql_select_login->bindParam(':task_id', $row_id);
		$sql_select_login->execute();
		$user_login = $sql_select_login->fetchColumn();
		return getUserName($user_login);
	}
}

if(empty($_GET['action'])){
	
}
else if($_GET['action'] == 'delete'){
	$sql = 'DELETE FROM task WHERE id = :task_id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':task_id', $_GET['id'], PDO::PARAM_INT);
	$stmt->execute();
}
else if($_GET['action'] == 'done'){
	$sql = 'UPDATE task SET is_done= :task_done WHERE id= :task_id';
	$task_done = 1;
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':task_done', $task_done);
	$stmt->bindParam(':task_id', $_GET['id'], PDO::PARAM_INT);	
	$stmt->execute();
}

if(!empty($_POST['description'])){
	if($_POST['save'] == 'Добавить'){
		
		$sql_select_user = $pdo->prepare('SELECT id FROM user WHERE login = :user');
		$sql_select_user->bindParam(':user', $_SESSION['user']);
		$sql_select_user->execute();
		
		$sql_insert = $pdo->prepare('INSERT INTO task(user_id, assigned_user_id, description, is_done, date_added) VALUES (:user_id, :assigned_user_id, :task_description, :is_done, :time_now)');		
		$sql_insert->bindParam(':user_id', $user_id);
		$sql_insert->bindParam(':assigned_user_id', $assigned_user_id);
		$sql_insert->bindParam(':task_description', $task_desctiption);
		$sql_insert->bindParam(':time_now', $time_now);
		$sql_insert->bindParam(':is_done', $done);
		

		$user_id = $sql_select_user->fetchColumn();
		$assigned_user_id = $user_id;
		$task_desctiption = strip_tags($_POST['description']);
		$time_now = $time;
		$done = '0';
		$inserted = $sql_insert->execute();
		header('Location: lesson_15.php');
	}
	else if($_POST['save'] == 'Сохранить'){
			
		$sql_insert = $pdo->prepare('UPDATE task SET description = :task_description, is_done = :is_done, date_added = :time_now WHERE id = :task_id');
		
		$sql_insert->bindParam(':task_id', $task_id);
		$sql_insert->bindParam(':task_description', $task_desctiption);
		$sql_insert->bindParam(':task_description', $task_desctiption);
		$sql_insert->bindParam(':time_now', $time_now);
		$sql_insert->bindParam(':is_done', $done);
		
		$task_id = strip_tags($_GET['id']);
		$task_desctiption = strip_tags($_POST['description']);
		$time_now = $time;
		$done = '0';
		$inserted = $sql_insert->execute();
		header('Location: lesson_15.php');
	}
}
?>
<html> 
<head>
<style>
    table {
        border-spacing: 0;
        border-collapse: collapse;
    }

    table td, table th {
        border: 1px solid #ccc;
        padding: 5px;
    }
    
    table th {
        background: #eee;
    }
</style>
<title>Список дел</title> 
</head> 
<body>
<h2>Список дел</h2>
<div style="float: left">
    <form method="POST">
        <input type="text" name="description" placeholder="Описание задачи" value="<?php
			if(empty($_GET['id']) or $_GET['action'] != 'edit'){
				echo '';
			}
			else{
				$sql = 'SELECT * FROM task WHERE id= :task_id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':task_id', $_GET['id'], PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetchAll();

				foreach ($result as $row) {
					echo strip_tags($row['description']);
				}
			}
			?>" />
        <input type="submit" name="save" value="<?php if(empty($_GET['action']) or $_GET['action'] != 'edit'){echo 'Добавить';} else{echo 'Сохранить';}?>" />
    </form>
</div>

<div style="float: left;margin-left: 20px;">
    <form method="GET">
        <label for="sort">Сортировать по:</label>
        <select name="sort_by">
			<?php
				if(empty($_GET['sort_by']) or $_GET['sort_by'] == 'date_created'){
					echo '<option selected value="date_created">Дате добавления</option>
					<option value="is_done">Статусу</option>
					<option value="description">Описанию</option>';
				}
				else if($_GET['sort_by'] == 'is_done'){
					echo'<option value="date_created">Дате добавления</option>
					<option selected value="is_done">Статусу</option>
					<option value="description">Описанию</option>';
				}
				else if($_GET['sort_by'] == 'description'){
					echo '<option value="date_created">Дате добавления</option>
					<option value="is_done">Статусу</option>
					<option selected value="description">Описанию</option>';
				}
			?>
        </select>
        <input type="submit" name="sort" value="Отсортировать" />
    </form>
</div>
<div style="clear: both"></div>
<table>
    <tr>
        <th>Описание задачи</th>
        <th>Дата добавления</th>
        <th>Статус</th>
        <th>Действия</th>
		<th>Ответственный</th>
		<th>Автор</th>
		<th>Закрепить задачу за пользователем</th>
    </tr>
<?php
if(!empty($_GET['sort_by'])){
	if($_GET['sort_by'] == 'date_created'){
		$sql = 'SELECT * FROM task ORDER BY date_added';
	}
	else if($_GET['sort_by'] == 'description'){
		$sql = 'SELECT * FROM task ORDER BY description';
	}
	else if($_GET['sort_by'] == 'is_done'){
		$sql = 'SELECT * FROM task ORDER BY is_done';
	}
	
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	$data = $stmt->fetchAll();
	
	foreach ($data as $row) {
		echo getTable($row);
	}
}
else{
	$sql = 'SELECT * FROM task ORDER BY date_added';

	foreach ($pdo->query($sql) as $row) {
		echo getTable($row);
	}	
}

?>
</table>
</body> 
</html>