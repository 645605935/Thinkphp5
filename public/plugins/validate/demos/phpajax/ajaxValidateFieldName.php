<?php

//@����PHP����������
//QQ��771943372
//ʱ�䣺2013��5��

$validateValue = $_REQUEST['fieldValue'];
$validateId = $_REQUEST['fieldId'];
$validateError = 'This username is already taken';
$validateSuccess = 'This username is available';
$arrayToJs = array();
$arrayToJs[0] = $validateId;

if ($validateValue == 'duncan') {
	$arrayToJs[1] = true;
	echo json_encode($arrayToJs);
}
else {
	for ($x = 0; $x < 1000000; $x++) {
		if ($x == 990000) {
			$arrayToJs[1] = false;
			echo json_encode($arrayToJs);
		}
	}
}

?>
