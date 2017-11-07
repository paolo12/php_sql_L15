<?php
$pdo = new PDO('mysql:host=localhost;dbname=global;charset=UTF8', 'root', 'qwerty');
date_default_timezone_set('Europe/Moscow');
$time = date("Y-m-d H:m:s");

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
		return '<tr>'.'<td>'.$row['description'].'</td>'.'<td>'.$row['date_added'].'</td>'.'<td id="center">'.$task_status.'</td>'.'<td>'.'<a href=?id='.$row['id'].'&action=edit>Изменить</a>'.' '.'<a href=?id='.$row['id'].'&action=done>Выполнить</a>'.' '.'<a href=?id='.$row['id'].'&action=delete>Удалить</a>'.'</td>'.'<td>'.$row['assigned_user_id'].'</td>'.'<td>'.$row['user_id'].'</td>'.'<td>'.$row['assigned_user_id'].'</td>'.'</tr>';
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
<?php
if(!empty($_POST['description'])){
	if($_POST['save'] == 'Добавить'){
		
		$sql_select_user = $pdo->prepare('SELECT id FROM user WHERE login = :user');
		echo'<br>';
		var_dump($sql_select_user);
		echo'<br>';
		$sql_select_user->bindParam(':user', $_SESSION['user']);
		echo'<br>';
		var_dump($sql_select_user);
		echo'<br>';
		
		$sql_insert = $pdo->prepare('INSERT INTO task(user_id, assigned_user_id, description, is_done, date_added) VALUES (:user_id, :assigned_user_id, :task_description, :is_done, :time_now)');		
		$sql_insert->bindParam(':user_id', $user_id);
		$sql_insert->bindParam(':assigned_user_id', $assigned_user_id);
		$sql_insert->bindParam(':task_description', $task_desctiption);
		$sql_insert->bindParam(':time_now', $time_now);
		$sql_insert->bindParam(':is_done', $done);
		

		$user_id = $sql_select_user->execute();
		echo'<br>';
		var_dump($user_id);
		echo'<br>';
		
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
<div style="clear: both"></div>
<table>
    <tr>
        <th>Описание задачи</th>
        <th>Дата добавления</th>
        <th>Статус</th>
        <th></th>
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
	
	var_dump($sql);
	foreach ($pdo->query($sql) as $row) {
		echo getTable($row);
	}	
}

?>
</table>
</body> 
</html>