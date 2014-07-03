<html>
<body>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
$data = json_decode(file_get_contents('data.json'), true);
//$purpose = 0;
/*	purpose = 0 僅顯示住家用
	purpose = 1 包括非住家用
*/
/*$word = '文山';
$year = 100;
$price_bottom = 0;
$price_top = 0;
$queue = 5;*/
/*  queue = 0 不排序
	queue = 1 建築完成年月
	queue = 2 交易年月
	queue = 3 總價元
	queue = 4 建物移轉總面積平方公尺
	queue = 5 單價每平方公尺
*/
$arr_data = array();
$arr_q = array();
$arr_c = array();

//$url = $_POST['url']; 
$word = $_POST['key']; 
$purpose = $_POST['purpose']; 
$year = $_POST['year'];
$price_bottom = $_POST['bottom']; 
$price_top = $_POST['top'];
$queue = $_POST['queue'];

//$data = json_decode(file_get_contents($url), true);

$price_H = 0;
$price_L = 0;
$count = 0;
$cost_sum = 0;
$cost_avg = 0;
$line = 0;
$line_h = 0;
$src = 0;
$src_h = 0;

/**********************/
/*** 所有自定義函式 ***/
/***			    ***/

/** 輸出範例 **/
/**			 **/
function print_()
{
	global $set, $count, $cost_sum;
	
	echo $set['鄉鎮市區'] . "	" 
		. $set['土地區段位置或建物區門牌'] . "	"
		. $set['建築完成年月'] . "	"
		. $set['交易年月'] . "	" 
		. $set['主要用途'] . "	"
		. $set['建物移轉總面積平方公尺'] . "	"
		. $set['單價每平方公尺'] . "	"
		. $set['總價元'] . "<br />";
		
	$cost_sum += $set['總價元'];
	$count++;
}

/** 尋找對應資料，並儲存資料 **/
/** 		 				 **/
function search_data()
{
	global $set, $word, $count, $year, $price_bottom, $price_top, $cost_sum;
	
	if (strpos($set['鄉鎮市區'], $word) != false 
	   || strpos($set['土地區段位置或建物區門牌'], $word) != false
	   || strncmp($set['鄉鎮市區'], $word, strlen($word) == 0)
	   || strncmp($set['土地區段位置或建物區門牌'], $word, strlen($word)) == 0)
	{
	    $cmp = $set['交易年月'] / 100;
		if ($cmp >= $year){
			if($price_bottom == 0 && $price_top ==0)
			{
				what_purpose();
				which_sort();
			}
			else if($price_bottom == 0){
				if($set['總價元'] < $price_top){
					what_purpose();
					which_sort();
				}
				else;
			}
			else if($price_top == 0){
				if($set['總價元'] > $price_bottom){
					what_purpose();
					which_sort();
				}
				else;
			}
			else{
				if($set['總價元'] < $price_top && $set['總價元'] > $price_bottom){
					what_purpose();
					which_sort();
				}
				else;
			}
		}
		else;
	}
	else;
}

/** 排序方式 **/
/** 		 **/
function which_sort()
{
	global $set, $count, $cost_sum, $queue, $arr_data, $arr_q, $arr_c;
	
	if(!$set['鄉鎮市區'])
		$set['鄉鎮市區'] = '-';
	if(!$set['土地區段位置或建物區門牌'])
		$set['土地區段位置或建物區門牌'] = '-';
	if(!$set['建築完成年月'])
		$set['建築完成年月'] = '-';
	if(!$set['交易年月'])
		$set['交易年月'] = '-';
	if(!$set['主要用途'])
		$set['主要用途'] = '-';
	if(!$set['建物移轉總面積平方公尺'])
		$set['建物移轉總面積平方公尺'] = '-';
	if(!$set['單價每平方公尺'])
		$set['單價每平方公尺'] = '-';
	if(!$set['總價元'])
		$set['總價元'] = '-';
		
	
	$arr_data[$count] =   $set['鄉鎮市區'] . " " 
						. $set['土地區段位置或建物區門牌'] . " " 
						. $set['建築完成年月'] . " "
						. $set['交易年月'] . " " 
						. $set['主要用途'] . " "
						. $set['建物移轉總面積平方公尺'] . " "
						. $set['單價每平方公尺'] . " "
						. $set['總價元'];
						
	$arr_c[$count] = $count;
	switch($queue)
	{
		case 0:	$cost_sum += $set['總價元'];
				$count++;
				price_bound();
				break;
		case 1:	if(!$set['建築完成年月'])
					$arr_q[$count] = 0;
				else
					$arr_q[$count] = $set['建築完成年月'];
				sort_queue();
				break;
		case 2:	if(!$set['交易年月'])
					$arr_q[$count] = 0;
				else
					$arr_q[$count] = $set['交易年月'];
				sort_queue();
				break;
		case 3:	if(!$set['總價元'])
					$arr_q[$count] = 0;
				else
					$arr_q[$count] = $set['總價元'];
				sort_queue();
				break;
		case 4:	if(!$set['建物移轉總面積平方公尺'])
					$arr_q[$count] = 0;
				else
					$arr_q[$count] = $set['建物移轉總面積平方公尺'];
				sort_queue();
				break;
		case 5:	if(!$set['單價每平方公尺'])
					$arr_q[$count] = 0;
				else
					$arr_q[$count] = $set['單價每平方公尺'];
				sort_queue();
				break;
		default:	
				break;
	}
}

/** 排序大小 **/
/** 		 **/
function sort_queue()
{
	global $set, $arr_data, $arr_q, $arr_c, $count, $cost_sum;
	
	if($count > 0){
		for($i=0; $i<$count; $i++)
		{
			if($arr_q[$count] > $arr_q[$i])
			{
				//echo $arr_q[0] . "<br />";
				$tem = $arr_q[$count];
				$tem2 = $arr_c[$count];
				for($j=$count; $j>$i; $j--){
					$arr_q[$j] = $arr_q[$j-1];
					$arr_c[$j] = $arr_c[$j-1];
				}
				$arr_q[$i] = $tem;
				$arr_c[$i] = $tem2;
				break;
			}
			else;
		}
	}
	else;
	
	$cost_sum += $set['總價元'];
	$count++;
	price_bound();
}

/** 最高和最低總價元 **/
/** 		 **/
function price_bound()
{
	global $set, $price_H, $price_L;
	
	if($set['總價元'] > $price_H)
		$price_H = $set['總價元'];
	else;

	if($price_L == 0)
		$price_L = $set['總價元'];
	else if($set['總價元'] < $price_L)
		$price_L = $set['總價元'];
	else;
}

/** 建物用途 **/
/** 		 **/
function what_purpose()
{
	global $set, $src, $src_h;
	
	if(strcmp($set['主要用途'], '住家用') == 0)
		$src_h++;
	else;
	$src++;
}	

/********************************************/
/*** 這裡開始讀檔、呼叫函式、輸出資料等等 ***/
/***									  ***/
echo '鄉鎮市區' . " /" 
	. '土地區段位置或建物區門牌' . " / "
	. '建築完成年月' . " / "
	. '交易年月' . " / " 
	. '主要用途' . " / "
	. '建物移轉總面積平方公尺' . " / "
	. '單價每平方公尺' . " / "
	. '總價元' . "<br />";
	
foreach ($data as $set)
{
	if($purpose == 0){
		if(strcmp($set['主要用途'], "住家用") == 0){
			search_data();
		}
		else;
	}
	else if($purpose == 1){
		search_data();
	}
	
	if(strcmp($set['主要用途'], "住家用") == 0)
		$line_h++;
	else;
	$line++;
}


/** 輸出資料格式 **/
/**				 **/
if($count == 0){
	echo "<br />";
	echo "沒有和 " . $word . " 相關的土地資訊。" . "<br />";
}
else{
	$cost_avg = $cost_sum / $count;
	$cost_avg = ceil($cost_avg);
	
	$src_ratio = ceil($src_h/$src*100);
	$line_ratio = ceil($line_h/$line*100);
	
	echo "<br />";
	echo "指定共 " . $count . " 筆資料" . "<br />";
	echo "最高總價元：" . $price_H . ", 最低總價元：" . $price_L . "<br />";
	echo "平均總價元：" . $cost_avg . "<br />";
	echo "指定住家用建築比率：" . $src_ratio . "%, " . "指定共 " . $src . " 筆資料, 住家用共 " . $src_h . " 筆資料" . "<br />";
	echo "所有住家用建築比率：" . $line_ratio . "%, " . "所有共 " . $line . " 筆資料, 住家用共 " . $line_h . " 筆資料" . "<br /><br />";
	
	echo '<table>';
	echo '<tr><th>鄉鎮市區</th>
		      <th>土地區段位置或建物區門牌</th>
			  <th>建築完成年月</th>
			  <th>交易年月</th>
			  <th>主要用途</th>
			  <th>建物移轉總面積平方公尺</th>
			  <th>單價每平方公尺</th>
			  <th>總價元</th></tr>';
	for($i=0; $i<$count; $i++) {
	
		$token = strtok($arr_data[$arr_c[$i]], " ");
		echo '<tr>';
		while ($token !== false)
		{
			echo '<td>' . $token . '</td>';
			$token = strtok(" ");
		}
		echo '</tr>';
		
	}
	echo '</table>';
}

?>
</body>
</html>
