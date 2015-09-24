<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-
transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head profile="http://gmpg.org/xfn/11">
  
  
</head>
<body>

<?php
echo '<table>';
if (is_array($resmio_apiData)){
	foreach($resmio_apiData as $titel => $value) {
		echo'<tr>';
		echo '<th align="left">'.$titel.'</th><th align="left">'.$value.'</th>';
		echo'</tr>';
	}
}
echo '</table>';
?>



</body>
</html>
