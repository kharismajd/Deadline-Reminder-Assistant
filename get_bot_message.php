<?php
include('database.inc.php');

$new_task_keywords = array();
$task_done_keywords = array();
$task_date_changed_keywords = array();
$tugas_keywords = array();

$sql = "select keyword from `new_task_keywords`";
$res = mysqli_query($con, $sql);
while ($row = mysqli_fetch_assoc($res))
{
	array_push($new_task_keywords, $row['keyword']);
}

$sql = "select keyword from `task_done_keywords`";
$res= mysqli_query($con, $sql);
while ($row = mysqli_fetch_assoc($res))
{
	array_push($task_done_keywords, $row['keyword']);
}

$sql = "select keyword from `task_date_changed_keywords`";
$res= mysqli_query($con, $sql);
while ($row = mysqli_fetch_assoc($res))
{
	array_push($task_date_changed_keywords, $row['keyword']);
}

$sql = "select keyword from `new_task_keywords` where type = 'Task'";
$res= mysqli_query($con, $sql);
while ($row = mysqli_fetch_assoc($res))
{
	array_push($tugas_keywords, $row['keyword']);
}

$txt = mysqli_real_escape_string($con,$_POST['txt']);

if (newTask($txt, $new_task_keywords) != -1)
{
	makeNewTask($con, $txt, $new_task_keywords);
}
else if (checkAllTaskWithType($txt, $new_task_keywords) != -1)
{
	printAllTask($con, checkAllTaskWithType($txt, $new_task_keywords));
}
else if (checkBetweenDateTaskWithType($txt, $new_task_keywords) != -1)
{
	printTaskBetweenDates($con, $txt, checkBetweenDateTaskWithType($txt, $new_task_keywords));
}
else if (checkNWeekTaskWithType($txt, $new_task_keywords) != -1)
{
	printNWeekTask($con, $txt, checkNWeekTaskWithType($txt, $new_task_keywords));
}
else if (checkNDayTaskWithType($txt, $new_task_keywords) != -1)
{
	printNDayTask($con, $txt, checkNDayTaskWithType($txt, $new_task_keywords));
}
else if (checkTodayTaskWithType($txt, $new_task_keywords) != -1)
{
	printTodayTask($con, checkTodayTaskWithType($txt, $new_task_keywords));
}
else if (checkAllTask($txt) != -1)
{
	printAllTask($con, NULL);
}
else if (checkBetweenDateTask($txt) != -1)
{
	printTaskBetweenDates($con, $txt, NULL);
}
else if (checkNWeekTask($txt) != -1)
{
	printNWeekTask($con, $txt, NULL);
}
else if (checkNDayTask($txt) != -1)
{
	printNDayTask($con, $txt, NULL);
}
else if (checkTodayTask($txt) != -1)
{
	printTodayTask($con, NULL);
}
else if (checkCourseCodeTask($txt) != -1)
{
	printCourseCodeTask($con, $txt, $tugas_keywords);
}
else if (isDoneTask($txt, $task_done_keywords) != -1)
{
	doneTask($con, $txt, $task_done_keywords);
}
else if (isChageDateTask($txt, $task_date_changed_keywords) != -1)
{
	changeDateTask($con, $txt, $task_date_changed_keywords);
}
else
{
	echo "Maaf, saya tidak mengerti<br>";
}

function checkAllTask($text)
{
	if (KMP("Deadline", $text) != -1 || KMP("Task", $text) != -1)
	{
		if (KMP("Sejauh ini", $text) != -1 || KMP("Saat ini", $text) != -1)
		{
			return 1;
		}
	}
	return -1;
}

function checkAllTaskWithType($text, $new_task_keywords)
{
	if (KMP("Sejauh ini", $text) != -1 || KMP("Saat ini", $text) != -1)
	{
		for ($i = 0; $i < count($new_task_keywords); $i++)
		{
			if (KMP($new_task_keywords[$i], $text) != -1)
			{
				return $new_task_keywords[$i];
			}
		}
	}
	return -1;
}

function printAllTask($con, $task_keyword)
{
	if ($task_keyword == NULL)
	{
		$query = "select * from tasks";
	}
	else
	{
		$query = "select * from tasks where type = '$task_keyword'";
	}

	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		if ($task_keyword == NULL)
		{
			echo "Tidak ada deadline<br>";
		}
		else
		{
			$lowercase_task_keyword = strtolower($task_keyword);
			echo "Tidak ada $lowercase_task_keyword<br>";
		}
	}
	else
	{
		echo "[Daftar Deadline]<br>";
		while ($row = mysqli_fetch_assoc($res))
		{
			echo "(ID: ". $row['id']. ") ". DateTime::createFromFormat('Y-m-d', $row['deadline'])->format('d/m/Y'). " - ". $row['course_id']. " - ". $row['type']. " - ". $row['topic']. "<br>";
		}
	}
}

function checkTodayTask($text)
{
	if (KMP("Deadline", $text) != -1 || KMP("Task", $text) != -1)
	{
		if (KMP("Hari ini", $text) != -1)
		{
			return 1;
		}
	}
	return -1;
}

function checkTodayTaskWithType($text, $new_task_keywords)
{
	if (KMP("Hari ini", $text) != -1)
	{
		for ($i = 0; $i < count($new_task_keywords); $i++)
		{
			if (KMP($new_task_keywords[$i], $text) != -1)
			{
				return $new_task_keywords[$i];
			}
		}
	}
	return -1;
}

function printTodayTask($con, $task_keyword)
{
	$date_now = date('Y-m-d');
	
	if ($task_keyword == NULL)
	{
		$query = "select * from tasks where deadline = '$date_now'";
	}
	else
	{
		$query = "select * from tasks where deadline = '$date_now' and type = '$task_keyword'";
	}

	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		if ($task_keyword == NULL)
		{
			echo "Tidak ada deadline untuk hari ini<br>";
		}
		else
		{
			$lowercase_task_keyword = strtolower($task_keyword);
			echo "Tidak ada $lowercase_task_keyword untuk hari ini<br>";
		}
	}
	else
	{
		echo "[Daftar Deadline]<br>";
		while ($row = mysqli_fetch_assoc($res))
		{
			echo "(ID: ". $row['id']. ") ". DateTime::createFromFormat('Y-m-d', $row['deadline'])->format('d/m/Y'). " - ". $row['course_id']. " - ". $row['type']. " - ". $row['topic']. "<br>";
		}
	}
}

function checkBetweenDateTask($text)
{
	if (KMP("Deadline", $text) != -1 || KMP("Task", $text) != -1)
	{
		$date_pattern = "/\d{2}\/\d{2}\/\d{4}/i";
		if (preg_match_all($date_pattern, $text, $matches))
		{
			if (count($matches[0]) == 2)
			{
				return 1;
			}
		}
	}
	return -1;
}

function checkBetweenDateTaskWithType($text, $new_task_keywords)
{
	$date_pattern = "/\d{2}\/\d{2}\/\d{4}/i";
	if (preg_match_all($date_pattern, $text, $matches))
	{
		if (count($matches[0]) == 2)
		{
			for ($i = 0; $i < count($new_task_keywords); $i++)
			{
				if (KMP($new_task_keywords[$i], $text) != -1)
				{
					return $new_task_keywords[$i];
				}
			}
		}
	}
	return -1;
}

function printTaskBetweenDates($con, $text, $task_keyword)
{
	$date_pattern = "/\d{2}\/\d{2}\/\d{4}/i";
	preg_match_all($date_pattern, $text, $matches);

	$date1 = DateTime::createFromFormat('d/m/Y', $matches[0][0])->format('Y-m-d');
	$date2 = DateTime::createFromFormat('d/m/Y', $matches[0][1])->format('Y-m-d');

	if ($task_keyword == NULL)
	{
		$query = "select * from tasks where deadline >= '$date1' and deadline <= '$date2'";
	}
	else
	{
		$query = "select * from tasks where deadline >= '$date1' and deadline <= '$date2' and type = '$task_keyword'";
	}

	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		if ($task_keyword == NULL)
		{
			echo "Tidak ada deadline di antara tanggal tersebut<br>";
		}
		else
		{
			$lowercase_task_keyword = strtolower($task_keyword);
			echo "Tidak ada $lowercase_task_keyword di antara tanggal tersebut<br>";
		}
	}
	else
	{
		echo "[Daftar Deadline]<br>";
		while ($row = mysqli_fetch_assoc($res))
		{
			echo "(ID: ". $row['id']. ") ". DateTime::createFromFormat('Y-m-d', $row['deadline'])->format('d/m/Y'). " - ". $row['course_id']. " - ". $row['type']. " - ". $row['topic']. "<br>";
		}
	}
}

function checkNDayTask($text)
{
	if (KMP("Deadline", $text) != -1 || KMP("Task", $text) != -1)
	{
		$day_pattern = "/(\d+)\s*hari ke depan/i";
		if (preg_match($day_pattern, $text, $matches))
		{
			return 1;
		}
	}
	return -1;
}

function checkNDayTaskWithType($text, $new_task_keywords)
{
	$day_pattern = "/(\d+)\s*hari ke depan/i";
	if (preg_match($day_pattern, $text, $matches))
	{
		for ($i = 0; $i < count($new_task_keywords); $i++)
		{
			if (KMP($new_task_keywords[$i], $text) != -1)
			{
				return $new_task_keywords[$i];
			}
		}
	}
	return -1;
}


function printNDayTask($con, $text, $task_keyword)
{
	$day_pattern = "/(\d+)\s*hari ke depan/i";
	preg_match($day_pattern, $text, $matches);
	$day_count_pattern = "/(\d+)/i";
	preg_match($day_count_pattern, $matches[0], $day_count);
	
	$date_now = date('Y-m-d');
	$date_now = new DateTimeImmutable($date_now);
	$date_Nday = $date_now->modify("+$day_count[0] day");

	$date_now_str = $date_now->format('Y-m-d');
	$date_Nday_str = $date_Nday->format('Y-m-d');
	
	if ($task_keyword == NULL)
	{
		$query = "select * from tasks where deadline >= '$date_now_str' and deadline <= '$date_Nday_str'";
	}
	else
	{
		$query = "select * from tasks where deadline >= '$date_now_str' and deadline <= '$date_Nday_str' and type = '$task_keyword'";
	}

	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		if ($task_keyword == NULL)
		{
			echo "Tidak ada deadline $day_count[0] hari dari sekarang<br>";
		}
		else
		{
			$lowercase_task_keyword = strtolower($task_keyword);
			echo "Tidak ada $lowercase_task_keyword $day_count[0] hari dari sekarang<br>";
		}
	}
	else
	{
		echo "[Daftar Deadline]<br>";
		while ($row = mysqli_fetch_assoc($res))
		{
			echo "(ID: ". $row['id']. ") ". DateTime::createFromFormat('Y-m-d', $row['deadline'])->format('d/m/Y'). " - ". $row['course_id']. " - ". $row['type']. " - ". $row['topic']. "<br>";
		}
	}
}

function checkNWeekTask($text)
{
	if (KMP("Deadline", $text) != -1 || KMP("Task", $text) != -1)
	{
		$day_pattern = "/(\d+)\s*minggu ke depan/i";
		if (preg_match($day_pattern, $text, $matches))
		{
			return 1;
		}
	}
	return -1;
}

function checkNWeekTaskWithType($text, $new_task_keywords)
{
	$day_pattern = "/(\d+)\s*minggu ke depan/i";
	if (preg_match($day_pattern, $text, $matches))
	{
		for ($i = 0; $i < count($new_task_keywords); $i++)
		{
			if (KMP($new_task_keywords[$i], $text) != -1)
			{
				return $new_task_keywords[$i];
			}
		}
	}
	return -1;
}

function printNWeekTask($con, $text, $task_keyword)
{
	$week_pattern = "/(\d+)\s*minggu ke depan/i";
	preg_match($week_pattern, $text, $matches);
	$week_count_pattern = "/(\d+)/i";
	preg_match($week_count_pattern, $matches[0], $week_count);
	
	$date_now = date('Y-m-d');
	$date_now = new DateTimeImmutable($date_now);
	$date_Nweek = $date_now->modify("+$week_count[0] week");

	$date_now_str = $date_now->format('Y-m-d');
	$date_Nweek_str = $date_Nweek->format('Y-m-d');

	if ($task_keyword == NULL)
	{
		$query = "select * from tasks where deadline >= '$date_now_str' and deadline <= '$date_Nweek_str'";
	}
	else
	{
		$query = "select * from tasks where deadline >= '$date_now_str' and deadline <= '$date_Nweek_str' and type = '$task_keyword'";
	}

	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		if ($task_keyword == NULL)
		{
			echo "Tidak ada deadline $week_count[0] minggu dari sekarang<br>";
		}
		else
		{
			$lowercase_task_keyword = strtolower($task_keyword);
			echo "Tidak ada $lowercase_task_keyword $week_count[0] minggu dari sekarang<br>";
		}
	}
	else
	{
		echo "[Daftar Deadline]<br>";
		while ($row = mysqli_fetch_assoc($res))
		{
			echo "(ID: ". $row['id']. ") ". DateTime::createFromFormat('Y-m-d', $row['deadline'])->format('d/m/Y'). " - ". $row['course_id']. " - ". $row['type']. " - ". $row['topic']. "<br>";
		}
	}
}

function checkCourseCodeTask($text)
{
	$course_code_pattern = "/Tugas\s*[A-Za-z]{2}\d{4}\s*/i";
	if (!preg_match($course_code_pattern, $text, $matches))
	{
		return - 1;
	}

	if (KMP("Deadline", $text) != -1)
	{
		return trim(substr($matches[0], 5));
	}

	return -1;
}

function printCourseCodeTask($con, $text, $tugas_keywords)
{
	$date_now = date('Y-m-d');
	$course_code = checkCourseCodeTask($text);
	if (count($tugas_keywords) == 1)
	{
		$query = "select * from tasks where type = '$tugas_keywords[0]'";
	}
	else
	{
		$query = "select * from tasks where (type = '$tugas_keywords[0]'";
	}

	for ($i = 1; $i < count($tugas_keywords) - 1; $i++)
	{
		$query = $query . " or type = '$tugas_keywords[$i]'";
	}

	$last_index = count($tugas_keywords) - 1;
	if (count($tugas_keywords) > 1)
	{
		$query = $query . " or type = '$tugas_keywords[$last_index]')";
	}

	$query = $query . " and LOWER(course_id) = LOWER('$course_code') and deadline > '$date_now'";

	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		echo "Tidak ada deadline dari mata kuliah tersebut<br>";
	}
	else
	{
		while ($row = mysqli_fetch_assoc($res))
		{
			echo DateTime::createFromFormat('Y-m-d', $row['deadline'])->format('d/m/Y') . "<br>";
		}
	}
}

function newTask($text, $new_task_keywords)
{
	for ($i = 0; $i < count($new_task_keywords); $i++)
	{
		$pattern = "/$new_task_keywords[$i]\s*[A-Za-z]{2}\d{4}\s*(.*)\s*pada\s*\d{2}\/\d{2}\/\d{4}/i";
		if (preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE))
		{
			return $matches[0][1];
		}
	}
	return -1;
}

function makeNewTask($con, $text, $new_task_keywords)
{
	$task_str = substr($text, newTask($text, $new_task_keywords));
	for ($i = 0; $i < count($new_task_keywords); $i++)
	{
		if (KMP($new_task_keywords[$i], $task_str) != -1)
		{
			$task_type = $new_task_keywords[$i];
			break;
		}
	}

	$course_code_pattern = "/[A-Za-z]{2}\d{4}/i";
	preg_match($course_code_pattern, $task_str, $course_code, PREG_OFFSET_CAPTURE);

	$pattern = "/pada\s*\d{2}\/\d{2}\/\d{4}/i";
	preg_match($pattern, $task_str, $date_start, PREG_OFFSET_CAPTURE);
	$topic = substr($task_str, $course_code[0][1] + 6, $date_start[0][1] - 1 - ($course_code[0][1] + 6));

	$deadline = substr($date_start[0][0], 5);
	$deadline = trim($deadline);
	$deadline = DateTime::createFromFormat('d/m/Y', $deadline);

	insertTaskDB($con, $course_code[0][0], $task_type, $topic, $deadline);
}

function isChageDateTask($text, $task_date_changed_keywords)
{
	$pattern = "/Task\s*(\d+)\s*/i";
	if (!preg_match($pattern, $text, $matches))
	{
		$pattern = "/Tugas\s*(\d+)\s*/i";
		if (!preg_match($pattern, $text, $matches))
		{
			return -1;
		}
	}

	$date = "/\d{2}\/\d{2}\/\d{4}/i";
	if (!preg_match($date, $text, $matches))
	{
		return -1;
	}

	for ($i = 0; $i < count($task_date_changed_keywords); $i++)
	{
		if (KMP($task_date_changed_keywords[$i], $text) != -1)
		{
			return 1;
		}
	}
	return -1;
}

function changeDateTask($con, $text, $task_date_changed_keywords)
{
	$task_id_pattern = "/Task\s*(\d+)\s*/i";
	if (preg_match($task_id_pattern, $text, $matches))
	{
		$task_id = substr($matches[0], 4);
	}

	$task_id_pattern = "/Tugas\s*(\d+)\s*/i";
	if (preg_match($task_id_pattern, $text, $matches))
	{
		$task_id = substr($matches[0], 5);
	}

	$date_pattern = "/\d{2}\/\d{2}\/\d{4}/i";
	preg_match($date_pattern, $text, $matches);
	$date = DateTime::createFromFormat('d/m/Y', $matches[0]);
	
	renewTaskDB($con, $task_id, $date);
}

function isDoneTask($text, $task_done_keywords)
{
	$pattern = "/Task\s*(\d+)\s*/i";
	if (!preg_match($pattern, $text, $matches))
	{
		$pattern = "/Tugas\s*(\d+)\s*/i";
		if (!preg_match($pattern, $text, $matches))
		{
			return -1;
		}
		else
		{
			$task_id = substr($matches[0], 5);
		}
	}
	else
	{
		$task_id = substr($matches[0], 4);
	}

	for ($i = 0; $i < count($task_done_keywords); $i++)
	{
		if (KMP($task_done_keywords[$i], $text) != -1)
		{
			return $task_id;
		}
	}
	return -1;
}

function doneTask($con, $text, $new_task_keywords)
{
	$id = isDoneTask($text, $new_task_keywords);
	deleteTaskDB($con, $id);
}

function insertTaskDB($con, $course_code, $type, $topic, $date)
{
	$strdate = $date->format('Y-m-d');
	$query = "insert into tasks (course_id, type, deadline, topic) values ('$course_code', '$type', '$strdate', '$topic');";
	mysqli_query($con, $query);
	if (mysqli_affected_rows($con) > 0)
	{
		$res = mysqli_query($con, "select * from tasks order by id desc limit 1");
		$row = mysqli_fetch_assoc($res);
		echo "[TASK BERHASIL DICATAT]<br>". "(ID: ". $row['id']. ") ". DateTime::createFromFormat('Y-m-d', $row['deadline'])->format('d/m/Y'). " - ". $row['course_id']. " - ". $row['type']. " - ". $row['topic'];
	}
	else
	{
		echo "Error: " . $query . "<br>" . mysqli_error($con);
	}
}

function renewTaskDB($con, $id, $new_date)
{
	$strdate = $new_date->format('Y-m-d');
	$query = "update tasks set deadline = '$strdate' where id = '$id'";
	mysqli_query($con, $query);
	if (mysqli_affected_rows($con) > 0)
	{
		echo "Task". $id. " diupdate";
	}
	else
	{
		echo "Task belum terdaftar";
	}
}

function deleteTaskDB($con, $id)
{
	$query = "delete from tasks where id = '$id'";
	mysqli_query($con, $query);
	if (mysqli_affected_rows($con) > 0)
	{
		echo "Task". $id. " ditandai selesai";
	}
	else
	{
		echo "Task belum terdaftar";
	}
}

function KMP($pattern, $text)
{
	$M = strlen($pattern);
	$N = strlen($text);
  
	$lps = LPSarray($pattern);
  
	$i = 0;
	$j = 0;
	while ($i < $N) {
		if (strtolower($pattern[$j]) == strtolower($text[$i]))
		{
			$j++;
			$i++;
		}
  
		if ($j == $M)
		{
			$idx = $i - $j;
			return $idx;
		}
  
		else if ($i < $N && strtolower($pattern[$j]) != strtolower($text[$i]))
		{
			if ($j == 0)
			{
				$i = $i + 1;
			}
			else
			{
				$j = $lps[$j - 1];
			}
		}
	}

	return -1;
}

function LPSarray($pattern)
{
	$len = 0;
  
	$lps = array();
	$lps[0] = 0;
  
	$i = 1;
	while ($i < strlen($pattern)) {
		if (strtolower($pattern[$i]) == strtolower($pattern[$len]))
		{
			$len++;
			$lps[$i] = $len;
			$i++;
		}
		else
		{
			if ($len != 0)
			{
				$len = $lps[$len - 1];
			}
			else
			{
				$lps[$i] = 0;
				$i++;
			}
		}
	}

	return $lps;
}
?>