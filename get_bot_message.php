<?php
include('database.inc.php');

$new_task_keywords = array();
$task_done_keywords = array();
$task_date_changed_keywords = array();

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

$txt = mysqli_real_escape_string($con,$_POST['txt']);
$sql = "select reply from questions where question like '%$txt%'";
$res = mysqli_query($con,$sql);

$date = DateTime::createFromFormat('d-m-Y', '28-05-2020');

if (isNewTask($txt, $new_task_keywords) != -1)
{
	makeNewTask($con, $txt, $new_task_keywords);
}
else if (isCheckAllTask($txt) != -1)
{
	printAllTask($con);
}
else if (isCheckBetweenDateTask($txt) != -1)
{
	printTaskBetweenDates($con, $txt);
}
else if (isCheckNWeekTask($txt) != -1)
{
	printNWeekTask($con, $txt);
}
else if (isCheckNDayTask($txt) != -1)
{
	printNDayTask($con, $txt);
}
else if (isCheckTodayTask($txt) != -1)
{
	printTodayTask($con);
}
else if (isDoneTask($txt, $task_done_keywords) != -1)
{
	doneTask($con, $txt, $task_done_keywords);
}
else if (isPostponeTask($txt, $task_date_changed_keywords) != -1)
{
	postponeTask($con, $txt, $task_date_changed_keywords);
}
else
{
	echo "Maaf, saya tidak mengerti<br>";
}

function isCheckAllTask($text)
{
	if (KMP("Deadline", $text) != -1 || KMP("Tugas", $text) != -1 || KMP("Task", $text) != -1)
	{
		if (KMP("Sejauh ini", $text) != -1)
		{
			return 1;
		}

		if (KMP("Saat ini", $text) != -1)
		{
			return 1;
		}
	}
	return -1;
}

function printAllTask($con)
{
	$query = "select * from tasks";
	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		echo "Tidak ada deadline<br>";
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

function isCheckTodayTask($text)
{
	if (KMP("Deadline", $text) != -1 || KMP("Tugas", $text) != -1 || KMP("Task", $text) != -1)
	{
		if (KMP("Hari ini", $text) != -1)
		{
			return 1;
		}
	}
	return -1;
}

function printTodayTask($con)
{
	$date_now = date('Y-m-d');
	$query = "select * from tasks where deadline = '$date_now'";
	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		echo "Tidak ada deadline untuk hari ini<br>";
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

function isCheckBetweenDateTask($text)
{
	if (KMP("Deadline", $text) != -1 || KMP("Tugas", $text) != -1 || KMP("Task", $text) != -1)
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

function printTaskBetweenDates($con, $text)
{
	$date_pattern = "/\d{2}\/\d{2}\/\d{4}/i";
	preg_match_all($date_pattern, $text, $matches);

	$date1 = DateTime::createFromFormat('d/m/Y', $matches[0][0])->format('Y-m-d');
	$date2 = DateTime::createFromFormat('d/m/Y', $matches[0][1])->format('Y-m-d');

	$query = "select * from tasks where deadline >= '$date1' and deadline <= '$date2'";
	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		echo "Tidak ada deadline di antara tanggal tersebut<br>";
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

function isCheckNDayTask($text)
{
	if (KMP("Deadline", $text) != -1 || KMP("Tugas", $text) != -1 || KMP("Task", $text) != -1)
	{
		$day_pattern = "/(\d+)\s*hari ke depan/i";
		if (preg_match($day_pattern, $text, $matches))
		{
			return 1;
		}
	}
	return -1;
}

function printNDayTask($con, $text)
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

	$query = "select * from tasks where deadline >= '$date_now_str' and deadline <= '$date_Nday_str'";
	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		echo "Tidak ada deadline di antara tanggal tersebut<br>";
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

function isCheckNWeekTask($text)
{
	if (KMP("Deadline", $text) != -1 || KMP("Tugas", $text) != -1 || KMP("Task", $text) != -1)
	{
		$day_pattern = "/(\d+)\s*minggu ke depan/i";
		if (preg_match($day_pattern, $text, $matches))
		{
			return 1;
		}
	}
	return -1;
}

function printNWeekTask($con, $text)
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

	$query = "select * from tasks where deadline >= '$date_now_str' and deadline <= '$date_Nweek_str'";
	$res = mysqli_query($con, $query);

	if (mysqli_num_rows($res) == 0) { 
		echo "Tidak ada deadline di antara tanggal tersebut<br>";
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

function isNewTask($text, $new_task_keywords)
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
	$task_str = substr($text, isNewTask($text, $new_task_keywords));
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

function isPostponeTask($text, $task_date_changed_keywords)
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

function postponeTask($con, $text, $task_date_changed_keywords)
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
	
	$pattern = "/Tugas\s*(\d+)\s*/i";
	if (!preg_match($pattern, $text, $matches))
	{
		return -1;
	}
	else
	{
		$task_id = substr($matches[0], 5);
		
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
	if (mysqli_query($con, $query))
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
	if (mysqli_query($con, $query))
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
        if (strtolower($pattern[$j]) == strtolower($text[$i])) {
            $j++;
            $i++;
        }
  
        if ($j == $M) {
			$idx = $i - $j;
			return $idx;
        }
  
        else if ($i < $N && strtolower($pattern[$j]) != strtolower($text[$i])) {
            if ($j != 0)
                $j = $lps[$j - 1];
            else
                $i = $i + 1;
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
        if (strtolower($pattern[$i]) == strtolower($pattern[$len])) {
            $len++;
            $lps[$i] = $len;
            $i++;
        }
        else
        {
            if ($len != 0) {
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