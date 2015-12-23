		<?php
            $sql_foodinfo = "select s.food_id,s.cata_name as food_name,s.price,p.cata_name from food_catalogue as s join food_catalogue as p where p.food_id = s.catalog_id and  s.food_id != s.catalog_id;";
            $result = $mysql->query($sql_foodinfo);
            $food_cata_info = array();
            echo "<form action ='submit.php' method = 'post'>";
            echo "<table class ='table-bordered'>"; 
            while($row = $mysql->fetch($result)) {
	            $food_cata_info['name'][$row['food_id']] = $row['food_name'];
	            $food_cata_info['price'][$row['food_id']] = $row['price'];
	            $food_cata_info['cata'][$row['food_id']] = $row['cata_name'];
            }$datetime = date('m-d-y h:i:s',time());
            echo "<th colspan='4'><span style='font-size: 26px;'>New Order</span> $datetime</th>";
            echo "<tr class='bold'><td>Food Type</td><td>Food Name</td><td>Price</td><td>Quantity</td></tr>"; 
		    $totalid = $mysql->fetch($mysql->query('select count(*) from food_catalogue;'))[0];
		    $create_res = array();
		    $totalp = 0;	
		    $itemcount = 0;
		    for ($f_id=11;$f_id<$totalid;$f_id++) {
				$f_quantity = (int)$_POST[$f_id];
	            if(!empty($f_quantity)) {
				    echo "<tr><td>".$food_cata_info['cata'][$f_id]."</td>";
	                echo "<td>".$food_cata_info['name'][$f_id]."</td>";
				    echo "<td>&#165;".$food_cata_info['price'][$f_id]."</td>";
                    echo "<td>".$f_quantity."</td></tr>";
				    $create_res['food_id'][$itemcount] =$f_id;
                    $create_res['quantity'][$itemcount]= $f_quantity;				
                    $totalp += $food_cata_info['price'][$f_id] * $f_quantity;
				    $itemcount++;
	            }
            }
		    if ($totalp > 0) {
	            if(!empty(preg_replace("/\s/","",(int)$_POST['cus_id']))){
					$sql_cusinfo = "SELECT customer_id,firstname,lastname,tel,address from customer_info where customer_id = '".preg_replace("/\s/","",(string)$_POST['cus_id'])."';";
					$result = $mysql->query($sql_cusinfo);  
					$row = $mysql->fetch($result);
					if(empty($row[0])){
						$row[0]= preg_replace("/\s/","",(string)$_POST['cus_id']);
					}
				}
		        echo "<tr><td colspan='2' class='bold'>Total Price</td><td colspan='2' class='text-centered'>&#165;  ".$totalp."</td></tr>";
		        echo "<tr><td colspan='1' class='bold'>Customer ID</td><td colspan='3'>".$row[0]."</td></tr>";
	          	echo "<tr><td colspan='1' class='bold'>Customer Name</td><td colspan='3'>".$row[1]." ".$row[2]."</td></tr>";
	         	echo "<tr><td colspan='1' class='bold'>Phone Number</td><td colspan='3'>".$row[3]."</td></tr>";
	         	echo "<tr><td colspan='1' class='bold'>Address</td><td colspan='3'>".$row[4]."</td></tr></table>";
	          	?>
				<blocks cols='3'>
				<div>
					<button type='button' outline name='back' onclick ="javascript: history.back(-1);">Modify</button>
				</div>
				<script language="javascript"> 
					function printdiv(printpage) { 
						var headstr = "<html><head><title></title></head><body>"; 
						var footstr = "</body>"; 
						var newstr = document.all.item(printpage).innerHTML; 
						var oldstr = document.body.innerHTML; 
						document.body.innerHTML = headstr+newstr+footstr; 
						window.print(); 
						document.body.innerHTML = oldstr; 
						return false; 
					} 
				</script> <!--<link href='' rel='stylesheet' media='all' />
					<link href='' rel='stylesheet' media='print'/>-->
	          	<div class='text-centered'>
					<button type='button' onclick="printdiv('create_page');">Print</button>
				</div>
	         	<div class='text-right'>
					<button class='submit' type='primary' name='submit'>Submit</button>
				</div>
				</blocks></form>
		    <?php
				session_start();
				$_SESSION['totalp'] = $totalp;
		        $_SESSION['cres'] = $create_res;
		        $_SESSION['cus_id']= $_POST['cus_id'];
            }else{
				//header("refresh:1;url='index.php?order_new='");
                echo "<div class='forms'><fieldset class='alert alert-error'><legend class='fat'>You Ordered Nothing</legend></fieldset></div>";
				//echo "<p>Back to Home Page in 1 seconds...</p>";
                //echo "<a href='index.php'>Back to Homepage immdiately</a>";
		    }	
        ?>
