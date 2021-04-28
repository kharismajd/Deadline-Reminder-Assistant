<?php
include('database.inc.php');

$keywords = array();
$sql = "select keyword from keywords";
$res= mysqli_query($con, $sql);
while ($row = mysqli_fetch_assoc($res))
{
	array_push($keywords, $row['keyword']);
}

$txt = mysqli_real_escape_string($con,$_POST['txt']);
$sql = "select reply from questions where question like '%$txt%'";
$res = mysqli_query($con,$sql);

$date = DateTime::createFromFormat('d-m-Y', '28-05-2020');

if (isNewTask($txt, $keywords) != -1)
{
	makeNewTask($con, $txt, $keywords, isNewTask($txt, $keywords));
}

function isNewTask($text, $keywords)
{
	for ($i = 0; $i < count($keywords); $i++)
	{
		$pattern = "/$keywords[$i]\s*[A-Za-z]{2}\d{4}\s*(.*)pada\s*\d{2}\/\d{2}\/\d{4}/i";
		if (preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE))
		{
			return $matches[0][1];
		}
	}
	return -1;
}

function makeNewTask($con, $text, $keywords, $index)
{
	$task_str = substr($text, $index);
	for ($i = 0; $i < count($keywords); $i++)
	{
		if (KMP($keywords[$i], $task_str) != -1)
		{
			$task_type = $keywords[$i];
			break;
		}
	}


	$course_code_pattern = "/[A-Za-z]{2}\d{4}/i";
	preg_match($course_code_pattern, $task_str, $course_code, PREG_OFFSET_CAPTURE);

	$pattern = "/pada\s*\d{2}\/\d{2}\/\d{4}/i";
	preg_match($pattern, $task_str, $date_start, PREG_OFFSET_CAPTURE);
	$topic = substr($task_str, $course_code[0][1] + 6, $date_start[0][1] - 1 - ($course_code[0][1] + 6));

	$deadline = substr($task_str, $date_start[0][1] + 5, $date_start[0][1] + 5 + 10 - ($date_start[0][1] + 5));
	echo $deadline;
	$deadline = DateTime::createFromFormat('d/m/Y', $deadline);

	insertTaskDB($con, $course_code[0][0], $task_type, $topic, $deadline);
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
		echo "true";
	}
	else
	{
		echo "Error: " . $query . "<br>" . mysqli_error($con);
	}
}

function deleteTaskDB($con, $id)
{
	$query = "delete from tasks where id = '$id'";
	mysqli_query($con, $query);
	if (mysqli_affected_rows($con) > 0)
	{
		echo "true";
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