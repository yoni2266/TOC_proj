TOC_proj
========
<html>
<body>
<meta 
	http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<?php 
	echo "<form name=\"form\" method=\"post\" action=\"op.php\">"; 
	//echo "URL : <input type=\"text\" name=\"url\" /> <br>"; 
	echo "建築物住宅土地關鍵字 : <input type=\"text\" name=\"key\" /> <br>"; 
	echo "是否指定住家用建築 : <input type=\"text\" name=\"purpose\" /> <br>";
	echo "(0)是, (1)否 <br><br>";
	echo "交易年月至今 : <input type=\"text\" name=\"year\" /> <br>";
	echo "總價元高於 : <input type=\"text\" name=\"bottom\" /> <br>"; 
	echo "總價元低於 : <input type=\"text\" name=\"top\" /> <br>"; 
	echo "排序方式 : <input type=\"text\" name=\"queue\" /> <br>";
	echo "(0)不排序<br>
		  (1)建築完成年月<br>
		  (2)交易年月<br>
		  (3)總價元<br>
		  (4)建物移轉總面積平方公尺<br>
		  (5)單價每平方公尺<br><br>";
	echo "<input type=\"submit\" name=\"button\" value=\"Search\" />"; 
	echo "</form>"; 
?>
</body>
</html>
