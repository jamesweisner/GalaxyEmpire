<?php

function humanFormat($n)
{
	if($n > 1000000000) return round($n / 1000000000, 2) . 'b';
	if($n > 1000000)    return round($n /    1000000, 2) . 'm';
	if($n > 1000)       return round($n /       1000, 2) . 'k';
	return $n;
}

function fuelRatio($tech)
{
	/* http://science.kennesaw.edu/~plaval/applets/QRegression.html
	L3 173.8 / 300 = 0.579333333
	L4 168.5 / 300 = 0.561666667
	L5 165.0 / 300 = 0.550000000
	L6 162.7 / 300 = 0.542333333
	L9 159.7 / 300 = 0.532333333
	*/
	return 1 - pow($tech, 3);
}

function battle($attacker, $defender)
{
	global $ships;

	$attacker['ship'] = $ships[(int) $attacker['type']];
	$defender['ship'] = $ships[(int) $defender['type']];

	if(!$attacker['ship']) return "Please specify attacking ship type.";
	if(!$defender['ship']) return "Please specify defending ship type.";

	if($attacker['count'] < 1) return "Please specify a positive number of attacking ships.";
	if($defender['count'] < 1) return "Please specify a positive number of defending ships.";

	$outcome['fuel'] = $attacker['count'] * $attacker['ship']['fuel'];
	$outcome['loot'] = $attacker['count'] * $attacker['ship']['cargo'];

	for($i = 0; $i < 3; $i++)
	{
		// Damage dealt to attacker by defender.
		$attacker['damage'] = $defender['count'] * max(0, $defender['ship']['attack'] - $attacker['ship']['shield']);
		$attacker['loss']   = min($attacker['count'], ceil($attacker['ship']['damage'] / $attacker['ship']['armor']));

		// Damage dealt to defender by attacker.
		$defender['damage'] = $attacker['count'] * max(0, $attacker['ship']['attack'] - $defender['ship']['shield']);
		$defender['loss']   = min($defender['count'], ceil($defender['ship']['damage'] / $defender['ship']['armor']));

		// Remove destroyed ships from the next round of battle.
		$attacker['count'] -= $attacker['loss'];
		$defender['count'] -= $defender['loss'];
	}

	$outcome['debris'] = array(0, 0, 0); // TODO.

	$outcome['attacker'] = $attacker;
	$outcome['defender'] = $defender;

	return $outcome;
}

$ships = json_decode(file_get_contents('ships.json'), true);

if($_POST['do'])
	$outcome = battle($_POST['attacker'], $_POST['defender']);

require_once('template.html');

?>
