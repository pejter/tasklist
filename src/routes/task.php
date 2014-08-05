<?php
$app->group('/task', function () use ($app,$db) {

	//task adding
	$app->get('/add', 'hasPerm', function () use ($app){
		$app->render('task-add.php');
	})->name('task-add');

	$app->post('/add', function () use ($app,$db){
		$post = $app->request->post();

		list($date, $time) = explode(' ', $post['due_date']);
		list($due_date['d'], $due_date['m'], $due_date['y']) = explode('-', $date);
		list($due_time['h'], $due_time['i']) = explode(':', $time);
		$post['due_date'] = mktime($due_time['h'], $due_time['i'], 0, $due_date['m'], $due_date['d'], $due_date['y']);
		$result = $db->task()->insert($post);

		if(!$result){
			$app->flash('error', 'Error adding task');
			$app->refresh;
		} else {
			$app->flash('success', 'Task added');
			$app->redirect('/');
		}
	});

	//task details
	$app->get('/:id',function ($id) use ($app,$db){
		$result = $db->task()->where('taskID',$id);

		if($task = $result->fetch()){
			$task['creator'] = $task->user['name'].' '.$task->user['surname'];
			$task['group'] = $task->groups['name'];
			$app->render('task-details.php',array(
				'task' => $task,
				'canEdit' => ($task->user['position'] <= 2)
			));
		} else
			echo "Task not found";
	})->name('task-view');

	//task editing
	$app->get('/:id/edit', 'hasPerm', function ($id) use ($app,$db){
		$result = $db->task()->where('taskID', $id);

		if($task = $result->fetch())
			$app->render('task-edit.php', array(
				'task' => $task
			));
	})->name('task-edit');

	$app->put('/:id/edit', 'hasPerm', function ($id) use ($app,$db){
		$result = $db->task()->where('taskID', $id);

		if($task = $result->fetch()){
			$put = $app->request->put();
			unset($put['_METHOD']);
			list($date, $time) = explode(' ', $put['due_date']);
			list($due_date['d'], $due_date['m'], $due_date['y']) = explode('/', $date);
			list($due_time['h'], $due_time['i']) = explode(':', $time);
			$put['due_date'] = mktime($due_time['h'], $due_time['i'], 0, $due_date['m'], $due_date['d'], $due_date['y']);
			if($put != $task->jsonSerialize()){

				if(!$task->update($put)){
					echo "Error!<br>Request: ";
					var_dump($put);
					echo "<br>Record: ";
					var_dump($task->jsonSerialize());
				} else {
					$app->flash('success', 'Task updated successfully');
					$app->redirect('/task/'.$put['taskID']);
				}
			} else
				$app->redirect('/task/'.$put['taskID']);
		} else {
			$app->flash('error', 'Task not found');
			$app->redirect('/');
		}

	});
});

?>