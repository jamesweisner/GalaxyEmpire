<?php

function humanFormat($n)
{
	if($n > 1000000000) return round($n / 1000000000, 2) . 'b';
	if($n > 1000000)    return round($n /    1000000, 2) . 'm';
	if($n > 1000)       return round($n /       1000, 2) . 'k';
	return $n;
}

function battle($attacker, $defender)
{
	$ships = json_decode(file_read_contents('ships.json'), true);

	list($attacker, $defender) = array($ships[$attacker], $ships[$defender]);

	if(!$attacker) return "Please specify attacking ship type.";
	if(!$defender) return "Please specify defending ship type.";

	$attacker['fuel'] = $attacker['count'] * $attacker['fuel'];
	$attacket['loot'] = $attacker['count'] * $attacker['cargo'];

	for($i = 0; $i < 3; $i++)
	{
		// Damage dealt to attacker by defender.
		$attacker['damage'] = $defender['count'] * max(0, $defender['attack'] - $attacker['shield']);
		$attacker['loss']   = min($attacker['count'], ceil($attacker['damage'] / $attacker['armor']));

		// Damage dealt to defender by attacker.
		$defender['damage'] = $attacker['count'] * max(0, $attacker['attack'] - $defender['shield']);
		$defender['loss']   = min($defender['count'], ceil($defender['damage'] / $defender['armor']));

		// Remove destroyed ships from the next round of battle.
		$attacker['count'] -= $attacker['loss'];
		$defender['count'] -= $defender['loss'];
	}

	return array($attacker, $defender);
}

if($_POST['random'] == $_SESSION['random'])
	$outcome = "Form validation failed.";
else
	$outcome = battle((int) $_POST['attacker'], (int) $_POST['defender']);

require_once('template.html');

?>
