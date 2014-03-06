<?php
//Creates a web browser dice game with dice images to display the rolls
//a player scores points for having 3, 4, or 5 of a kind
//the player at the end of the desired number of rounds with the highest score wins
session_start();
$_SESSION['players'] = $_POST['players'];
$_SESSION['names'] = $_POST['names']; //names array
$_SESSION['numrounds']= $_POST['rounds']; //number of rounds
$eles = array();

if(!isset($_SESSION['player'])){
	$_SESSION['player']=1;
	$_SESSION['round'] =1;
	$_SESSION['scores']= array();
}

if($_SESSION['round']<= $_SESSION['numrounds']){
	if($_POST['state']=="Roll"){	
		$dice = roll(); //dice contains the values of each die for the roll
		$diceCount = countDice($dice);
		$_SESSION['scores'][$_SESSION['player']][$_SESSION['round']] = getScore($diceCount);
		$eles['state'] = $_SESSION['state'];
		$eles['table'] = printTable($dice);
	}
	elseif($_POST['state']=="Reroll"){
		$dice = roll3($_SESSION['key'], $_SESSION['savedDice']);
		$diceCount = countDice($dice);
		$_SESSION['scores'][$_SESSION['player']][$_SESSION['round']] = getScore($diceCount);
		$eles['state'] = $_SESSION['state'];
		$eles['table']= printTable($dice);
	}
}	
else{
	$eles['state'] = "Gameover";
	$_SESSION= array();
}

	

function getScore($count){
	if(max($count) == 3){
		$value = 3;
		$_SESSION['state'] = "Roll";
	}
	elseif(max($count)==4){
		$value = 6;
		$_SESSION['state'] = "Roll";
	}
	elseif(max($count)==5){
		$value = 12;
		$_SESSION['state'] = "Roll";
	}
	elseif(max($count)==2){
		if($_POST['roll'] == 1){ // if it is players first roll set state to reroll and save the pair from first roll in an array
			$key = array_keys($count, max($count));
			$_SESSION['savedDice'] = array($key[0], $key[0]);
			$_SESSION['state'] = "Reroll";
			$value = 0; //return value 0 and set state to reroll to change interface and reroll for same player
		}
		else{
			$value = 0;
			$_SESSION['state'] = "Roll";
		}
	}
	else{
		$value = 0;
		$_SESSION['state'] = "Roll";
	}
	
	return $value;
}

function countDice($rolledDice){ //counts the number of each dice in the roll to an array so the roll can be scored 
	for($i=0;$i<sizeof($rolledDice);$i++){
		switch($rolledDice[$i]){
			case 1:
				$count[1] += 1;
				break;
			case 2:	
				$count[2] += 1;
				break;
			case 3:
				$count[3] += 1;
				break;
			case 4:
				$count[4] += 1;
				break;
			case 5:
				$count[5] += 1;
				break;
			default:
		}
	}
	return $count; //return the dice array so it can be scored
}

function roll(){  //the roll function to get 5 dice if it is the first players roll
	$roll = array();
	for($i = 0; $i<5; $i++){
		$roll[$i]= rand(1, 6);
	}
	return $roll; 
}

function roll3($key, $oldDice){ //function to get 3 new dice for a player if they have to reroll
	for($i=0; $i<3; $i++){
		$roll3 =rand(1,6);
		array_push($oldDice, $roll3); //pushes the 3 new dice onto the array 
	}
	return $oldDice;
}

function printTable($dice){ //prints the table for 
	$table .= '<table class="table table-striped"><tr><th colspan="'.(sizeof($_SESSION['names'])+1).'"">Scoreboard</th></tr><tr><td>Rounds</td>';
	for($i=0; $i<sizeof($_SESSION['names']); $i++){
		$table .= '<td>'.$_SESSION['names'][$i].'</td>';
	}
	$table.='</tr>';
	for($j=1; $j<=$_SESSION['numrounds']; $j++){
		$table.='<tr><td>Round '.$j.'</td>';
		for($k=1; $k<=$_SESSION['players']; $k++){
			$table.='<td>'.$_SESSION['scores'][$k][$j].'</td>';
		}
		$table.='</tr>';
	}
	$table.='<tr><td>Totals</td>';
	for($k=1; $k<=$_SESSION['players']; $k++){
			$table.='<td>'.(array_sum($_SESSION['scores'][$k])).'</td>';
	}
	$table.='</tr></table>';
	return $table;	
}

if($_SESSION['state']== "Roll")
	$_SESSION['player']++;

if($_SESSION['player'] > $_SESSION['players']){
		$_SESSION['round']++;
		$_SESSION['player']=1;
}
$eles['savedDice'] = $_SESSION['savedDice'];
$eles['numrounds'] = $_SESSION['numrounds'];
$eles['players'] = $_SESSION['players'];
$eles['dice'] =$dice;
$eles['key'] = $_SESSION['key'];
echo json_encode($eles);
//print_r($eles);
?>

