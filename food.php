<script src="static/js/jquery-1.7.min.js"></script>
<script src="static/js/jquery.jqprint.js"></script>
<!-- jQprint -->
<script>
/**functions of change data in report*/
	function changeQuan(id,valu){
		document.getElementById(id).innerHTML = valu;	
	}
	function addQuan(id,valu){
		var orig =  document.getElementById(id).innerHTML;
		var sum = parseInt(orig)+parseInt(valu);
		document.getElementById(id).innerHTML = sum;
	}
	function print(){
		$(document).ready(function() { 
			$(".my_show").jqprint(); 
			});
	}
	function firm(text,id){  
		if(confirm(text)){
			document.getElementById(id).submit();
		}else{
			return false;
		}
	}
</script>
<?php
if(isset($_GET['action'])){
	$action = $_GET['action'];
}else{
	$action = 'detail';
}
$sql_fcata = "select catalog_id,cata_name from food_catalogue where price IS NULL ORDER by catalog_id";
$result_fcata = $mysql->query($sql_fcata);
if($action == 'cata'){
/**Show Catalogue of food*/
    echo "<table class ='table-stripped'>
			<th colspan='2'>Food catalogue:</th>
			<tr>
				<td class='bold'>Catalogue ID</td><td class='bold'>Catalogue Name</td>
			</tr>";
    while($row_fcata = $mysql->fetch($result_fcata)) {
        echo "<tr>
				<td>".$row_fcata['catalog_id']."</td>
				<td>".$row_fcata['cata_name']." </td>
			</tr>";
    }
    echo "</table>";
}else if($action == 'detail'){
/**Show food detail*/
	$sql_fdetail = "select s.food_id,s.cata_name as food_name,s.price,p.cata_name from food_catalogue as s join food_catalogue as p where p.catalog_id = s.catalog_id and s.price IS NOT NULL GROUP BY food_id"; 
	    $result = $mysql->query($sql_fdetail);
		
        echo "<table class ='table-stripped'>
				<th colspan='6'>Food Category:</th>
				<tr>
					<td class='bold'>Food ID</td>
					<td class='bold'>Food Name</td>
					<td class='bold'>Food Price</td>
					<td class='bold'>Food Type</td>
					<td style='width:1%;'></td>
					<td style='width:1%;'></td>
				</tr>";
        while($row = $mysql->fetch($result)) {
            echo "<tr>
					<td>".$row['food_id']."</td>
					<td>".$row['food_name']." </td>
					<td>&#165;".$row['price']." </td>
					<td>".$row['cata_name']." </td>
					<td><samp onclick=\"firm('Do you want to Edit this Food?','editFood{$row['food_id']}')\">E</samp></td>
					<td><kbd onclick=\"firm('Do you want to Delete this Food?','deleteFood{$row['food_id']}')\">X</kbd></td>
				</tr>";
			echo "<form action='index.php?page=food&action=new' method='post' id='editFood{$row['food_id']}'>
					<input type='hidden' name='origFoodEdit' value='{$row['food_id']},{$row['food_name']},{$row['price']},{$row['cata_name']}'/>
				</form>
				<form action='' method='post' id='deleteFood{$row['food_id']}'>
					<input type='hidden' name='origFoodDel' value='{$row['food_id']}'/>
				</form>";
        }
        echo "</table>";
		if(isset($_POST['origFoodDel'])){
			$sql_delFood = "DELETE FROM food_catalogue WHERE food_id = {$_POST['origFoodDel']}";
			$mysql->query($sql_delFood);
			echo "<script>window.location.href='index.php?page=food&action=detail';</script>";
		}
}else if($action == 'sold'){
/**Show food sold information*/
	echo "<table class ='table-stripped'>"; 
	while($row_fcata = $mysql->fetch($result_fcata)) {
		$cata_id = $row_fcata['catalog_id'];	
        $sql_finfo = "select food_id,s.cata_name as food_name,s.price from food_catalogue as s where s.catalog_id = ".$cata_id." and s.price IS NOT NULL;";
	    $result_finfo = $mysql->query($sql_finfo); 
        echo "<th colspan='3' id=".$row_fcata['cata_name'].">".$row_fcata['cata_name']."</th>
				<tr>
					<td  class='text-centered'><b>Food Name</b></td>
					<td class='bold'>Price</td><td class='bold'>Quantity</td>
				</tr>";
		while($row_finfo = $mysql->fetch($result_finfo)) {
			echo "<tr>
					<td class='text-centered'>".$row_finfo['food_name']."</td>
					<td>&#165;".$row_finfo['price']." </td>";   
            $foodid = $row_finfo['food_id'];
            $sql_fquantity = "SELECT sum(quantity)as Quantity from order_food where food_id = ".$foodid.";";
			$result_fquantity = $mysql->query($sql_fquantity); 
  			while($row_fquantity = $mysql->fetch($result_fquantity)) {
		        echo "<td>".$row_fquantity['Quantity']."</td>
				</tr>";
		    }
	    }   
    }echo "</table>";
}else if($action == 'new'){
	echo "<form action='submit.php' method='post'>
		<table>
			<th colspan='4'> 
				<label>New Food&nbsp;<input type='radio' name='isCata' value='food' onclick='refresh()' checked>&nbsp; &nbsp;</label><label>&nbsp; &nbsp;
				New Food Category <input type='radio' name='isCata' value='cata' onclick='hideCata()'></label>
			</th>
		<script>
			function refresh(){
				window.location.href='index.php?page=food&action=new';
			}
			function hideCata(){
				var cata = document.getElementsByClassName('hideCata');
				for(var i = 0; i < cata.length;i++){
					cata[i].style.display = 'none';
				}
			}
		</script>";
	$sql_LastFoodID = 'SELECT food_id FROM food_catalogue ORDER BY food_id DESC LIMIT 1';
	$foodId = $mysql->fetch($mysql->query($sql_LastFoodID))[0]+1;
	echo"		<tr>
				<td class='bold' class='width-2'>Food ID</td>
				<td class='width-2'>
					<input type='number' name='origId' maxlength='6' value='$foodId' disabled='disabled'/>
					<input type='hidden' name='cataId' value=$foodId />
				</td>
			</tr>
			<tr>
				<td class='bold'>Food Name<span class='req'> *</span></td>
				<td>
					<input type='text' maxlength='10' name='foodName' required/>
				</td>
				<td class='bold'><span class='hideCata'>Food Type<span class='req'> *</span></span></td>
				<td class='width-4'><span class='hideCata'>
					<div style='margin-left:-200px;'><b>Catalogue:</b>
						<select name='foodCata' class='width-6'>";
	$sql_FoodCata = "SELECT catalog_id,cata_name FROM food_catalogue WHERE price is NULL ORDER BY cata_name";						
	$result_FoodCata = $mysql->query($sql_FoodCata);	
	$foodCata = array();
		while($row = $mysql->fetch($result_FoodCata)) {
			echo "<option value=$row[0]>$row[1]</option>";
			$foodCata[$row[1]] = $row[0];
		}	
	echo"				</select>
					</div><span>
				</td>
			</tr>
			<tr>
				<td class='bold'><span class='hideCata'>Price<span class='req'> *</span></span></td>
				<td><span class='hideCata'>
					<label>&#165;&nbsp;</label><input type='number' max='999' name='price' required/>
				</span></td>
			</tr>
		</table>
		<div class='text-right'>
			<button class='submit' type='primary' name='submit'>Submit</button>
		</div>
	</form>";
	if(isset($_POST['origFoodEdit'])){
			$origFood = explode(',',$_POST['origFoodEdit']);
			echo "<script>
					document.getElementsByName('isCata')[1].disabled = true;
					var foodid = document.getElementsByName('origId')[0];
					foodid.disabled = false;
					document.getElementsByName('cataId')[0].disabled = true;
					foodid.value = {$origFood[0]};
					foodid.onchange = function(){
						foodid.value = {$origFood[0]};
					};
					document.getElementsByName('foodName')[0].value = '{$origFood[1]}';
					document.getElementsByName('price')[0].value = '{$origFood[2]}';
					document.getElementsByName('foodCata')[0].value = '{$foodCata[$origFood[3]]}';
				</script>";
	}
}else if($action== 'weekly'){
	/*echo "<style>
			input[type=range]:before { content: attr(min); padding-right: 5px; }
			input[type=range]:after { content: attr(max); padding-left: 5px;}
		</style>
		<script>function changenum(){
					document.getElementById('rangeres').innerHTML = document.getElementById('weeknum').value;
				}
		</script>
		<b>Week <span id='rangeres'><span></b>
		<input type='range' id='weeknum' step='1' min='0' max='53' onchange='changenum()'/>";*/
/**show weekly report*/
/**a form to change week and year, default is now*/
	$timeres = $mysql->fetch($mysql->query('select year(now()),week(now(),1)'));
	$weeknum = $timeres[1];
	$yearnum = $timeres[0];
	echo "<div class='my_show'>
			<form method='post' action='index.php?page=food&action=weekly'>
				Week:<input type='number' name='weeknum' id='weeknum' placeholder='Week' min='0' max='53' value='$weeknum'/>
				Year:<input type='number' name='yearnum' id='yearnum' placeholder='Year' min='2010' max='$yearnum' value='$yearnum'/>
				<button type='submit' value='OK' class='my_hidden'>OK</button>
			</form>
			<button style='margin-left:76%;margin-top:-4%;float:right;position:absolute' type='primary' onclick='print()'>Print</button>";
	if(isset($_POST['weeknum'])&&$_POST['weeknum']!=''){$weeknum = $_POST['weeknum'];}
	if(!empty($_POST['yearnum'])){$yearnum = $_POST['yearnum'];}
	echo "<script>
			document.getElementById('weeknum').value = $weeknum;
			document.getElementById('yearnum').value = $yearnum;
		</script>";
/**active sql to find the date of Mon. to Sat.*/	
	$sql_subdate = "select DATE_ADD('$yearnum-01-01',INTERVAL (7*$weeknum-WEEKDAY('$yearnum-01-01')) DAY) AS start, DATE_ADD(DATE_ADD('$yearnum-01-01',INTERVAL (7*$weeknum-WEEKDAY('$yearnum-01-01')) DAY),INTERVAL 5 DAY) AS end;";
	$subdate = $mysql->fetch($mysql->query($sql_subdate));
/**show weekly report table*/
	echo "<table class ='table-stripped'>
			<th style='font-size:1.6em' class='text-centered' colspan='10'>Weekly's selling Food Diary: {$subdate['start']} to {$subdate['end']}</th>"; 
	while($row_fcata = $mysql->fetch($result_fcata)) {
		$cata_id = $row_fcata['catalog_id'];	
        $sql_finfo = "SELECT food_id,s.cata_name AS food_name,s.price FROM food_catalogue AS s WHERE s.catalog_id = ".$cata_id." AND s.price IS NOT NULL;";
	    $result_finfo = $mysql->query($sql_finfo); 
        echo "<tr id='{$row_fcata['cata_name']}'>
				<td class='text-centered'><b style='font-size:1.2em;'>{$row_fcata['cata_name']}</b></td>
				<td class='bold'>Price</td>
				<td class='bold'>Monday</td>
				<td class='bold'>Tuesday</td>
				<td class='bold'>Wednesday</td>
				<td class='bold'>Thursday</td>
				<td class='bold'>Friday</td>
				<td class='bold'>Saturday</td>
				<td class='bold'>Quantity</td>
			</tr>";
		while($row_finfo = $mysql->fetch($result_finfo)) {
			echo "<tr>
					<td class='text-centered'>".$row_finfo['food_name']."</td>
					<td>&#165;".$row_finfo['price']." </td>
					<td id='1_{$row_finfo['food_id']}'></td>
					<td id='2_{$row_finfo['food_id']}'></td>
					<td id='3_{$row_finfo['food_id']}'></td>
					<td id='4_{$row_finfo['food_id']}'></td>
					<td id='5_{$row_finfo['food_id']}'></td>
					<td id='6_{$row_finfo['food_id']}'></td>
					<td id='q_{$row_finfo['food_id']}'>0</td>
				</tr>";
	    }echo "<tr>
					<td class='text-centered'>TOTAL</td>
					<td></td>
					<td>&#165;<span id='1_$cata_id'>0</span></td>
					<td>&#165;<span id='2_$cata_id'>0</span></td>
					<td>&#165;<span id='3_$cata_id'>0</span></td>
					<td>&#165;<span id='4_$cata_id'>0</span></td>
					<td>&#165;<span id='5_$cata_id'>0</span></td>
					<td>&#165;<span id='6_$cata_id'>0</td>
					<td id='q_$cata_id'>0</td>
				</tr>
				<tr>
					<td class='text-centered'><b>Total Week: </b></td>
					<td><kbd>&#165;<span id='total_$cata_id'>0</span></kbd></td>
				<tr/>";
	}
	echo "<tr>
			<td class='fat'><b>AMOUNT TOTAL:&nbsp; &nbsp; <samp>&#165;<span id='total_all'>0</span></b></samp></td>
			<td></td>
			<td>&#165;<span id='day1'>0</span></td>
			<td>&#165;<span id='day2'>0</span></td>
			<td>&#165;<span id='day3'>0</span></td>
			<td>&#165;<span id='day4'>0</span></td>
			<td>&#165;<span id='day5'>0</span></td>
			<td>&#165;<span id='day6'>0</span></td>
		  </tr>
		</table>
	</div>";
/**stastic data and write it on the report table*/
	for($dayweek=2;$dayweek<8;$dayweek++){
		$sql="select of.food_id,sum(quantity),sum(quantity)*fc.price,fc.catalog_id from order_food as of join food_catalogue as fc ON of.food_id = fc.food_id where order_id in (select order_id from orders where week(date,1) = $weeknum and year(date)=$yearnum and DAYOFWEEK(date) = $dayweek) group by food_id";
		$res = $mysql->query($sql);
		$zhou = $dayweek - 1;
		while($row = $mysql->fetch($res)){
			echo "<script>changeQuan('{$zhou}_{$row[0]}',{$row[1]});
						addQuan('q_{$row[0]}',{$row[1]});
						addQuan('{$zhou}_{$row[3]}',{$row[2]});
						addQuan('q_{$row[3]}',{$row[1]});
						addQuan('total_{$row[3]}',{$row[2]})
						addQuan('total_all',{$row[2]});
						addQuan('day{$zhou}',{$row[2]});
				</script>";
		}
	}
}
?>
