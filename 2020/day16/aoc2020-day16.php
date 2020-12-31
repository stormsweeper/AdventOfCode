<?php

//Runs on PHP 8, not tested on <=7.x
$start = microtime( true );

#use JetBrains\PhpStorm\ArrayShape; //Comment out if you don't know what this is

#[ArrayShape( [ 'name' => "string", 'fromA' => "int", 'toA' => "int", 'fromB' => "int", 'toB' => "int" ] )] //and this
function parse_rule(
	string $rule
): array {

	$re      = "/([a-z ]*): (\d+)-(\d+) or (\d+)-(\d+)/";
	$matches = [];

	preg_match( $re, $rule, $matches );

	$name  = $matches[1];
	$fromA = $matches[2];
	$toA   = $matches[3];
	$fromB = $matches[4];
	$toB   = $matches[5];

	return [ //could be an object? yes. Do I want to refactor everything? that's right.
		'name'  => $name,
		'fromA' => $fromA,
		'toA'   => $toA,
		'fromB' => $fromB,
		'toB'   => $toB
	];
}

function parse_ticket( string $ticket ): array { // function with apt name >> stdlib function

	return array_map('intval', explode( ",", $ticket ));

}

function validate( int $val, array $ruleSet ): bool { //validate by ruleset, used in part 1

	$valid = false;

	foreach ( $ruleSet as $rule ) {
		if (
			( $val >= $rule['fromA'] and $val <= $rule['toA'] ) or
			( $val >= $rule['fromB'] and $val <= $rule['toB'] )
		) {
			$valid = true;
		}
	}

	return $valid;

}


function validate_ticket( array $ticket, array $ruleSet ): bool { //application of above function on a full ticket

	foreach ( $ticket as $field ) {
		if ( ! validate( $field, $ruleSet ) ) {
			return false;
		}
	}

	return true;

}

function validate_by_rule( int $val, array $rule ): bool { //used by part 2, self explanatory

	if (
		( $val >= $rule['fromA'] and $val <= $rule['toA'] ) or
		( $val >= $rule['fromB'] and $val <= $rule['toB'] )
	) {
		return true;
	} else {
		return false;
	}
}

$input = file_get_contents( 'input.txt' ); // <- your file name here

$input = explode( "\n\n", $input );

$rawRules = explode( "\n", $input[0] ); //array of strings
$my       = $input[1];
$nearby   = $input[2];

$my     = preg_replace( "/.*:\n/", "", $my );
$nearby = explode( "\n", preg_replace( "/.*:\n/", "", $nearby ) );
$rules  = [];
foreach ( $rawRules as $rawRule ) {
	$rules[] = parse_rule( $rawRule );
} //now $rules is an array of arrays! see ArrayShape above

$my      = parse_ticket( $my ); //Hello $my, see you on row 201
$tickets = [];
foreach ( $nearby as $ticket ) {
	$tickets[] = parse_ticket( $ticket );
}

$allTickets = array_merge( ...$tickets ); //to solve part 1 we don't care about single tickets, every integer in a single array
$notValid   = [];

foreach ( $allTickets as $val ) {
	if ( ! validate( $val, $rules ) ) {
		$notValid[] = $val; //value not valid? strait to jail
	}
}

echo "Part 1: " . array_sum( $notValid ) . PHP_EOL;

/* ********* End of Part 1 ********

           __  _
       .-.'  `; `-._  __  _
      (_,         .-:'  `; `-._
    ,'o"(        (_,           )
   (__,-'      ,'o"(            )>
      (       (__,-'            )
       `-'._.--._(             )
          |||  |||`-'._.--._.-'
                     |||  |||

/* ************ Part 2 ************ */

$valid = [];
foreach ( $tickets as $ticket ) {
	if ( validate_ticket( $ticket, $rules ) ) {
		$valid[] = $ticket;
	}
}
// $valid array of valid tickets as [1,2,3,4,5...]
$valid_fields = [];
foreach ( $valid as $ticket ) {
	$i = 0;
	foreach ( $ticket as $field ) {

		$valid_fields[ $i ][] = $field;
		$i ++;
	}
}
// $valid_field array of arrays (each subarray all the values of a field)
//ex: for passports 1,2,3 4,5,6 and 7,8,9 $valid_fields = [ [1,4,7], [2,5,8], [3,6,9] ]

$ruleAssoc  = [];
$found      = [];
$ruleSearch = $rules;
$i          = 0;
foreach ( $valid_fields as $field_col ) { //foreach field

	foreach ( $ruleSearch as $rule ) { //foreach rule as associative array (see parse_rule)

		$validity = array_map( function ( $val ) use ( $rule ) {
			return validate_by_rule( $val, $rule );
		}, $field_col ); //returns array of bools


		//if all bools are true the constraint (=column) "$rule['name']" is valid for $i-th field
		$ruleAssoc[ $i ][ $rule['name'] ] = ! in_array( false, $validity );


	}
	$i ++; // $k => $v foreach are so overrated,lol
}

ksort( $ruleAssoc ); //order by key plz

$fields_ok     = [];
$excluded_cols = [];

//$ruleAssoc is a matrix, ROWS are all values in x position from all passports,
//COLUMNS are all available fields. $ruleAssoc[$x][$col] = true if $col can be a field for row #x

while ( count( $fields_ok ) != 20 ) { //While any field not named remains
	foreach ( $ruleAssoc as $field_key => $field_row ) { //$key = field ordinal, $field_row = [$col_name => $valid_for_field]

		$field_row = array_filter( $field_row, function ( $k, $v ) use ( $excluded_cols ) { //Exclude already assigned columns
			return ! in_array( $v, $excluded_cols );
		}, ARRAY_FILTER_USE_BOTH );

		$score = array_sum( $field_row ); //$sum of n booleans is = to number of true values, in this case number
		// of valid columns for the field

		if ( $score == 1 ) { //sudoku!
			$col_found               = trim( join( "", array_keys( array_filter( $field_row ) ) ) ); //name of the column
			$fields_ok[ $field_key ] = $col_found; //set as found
			$excluded_cols[]         = $col_found; //exclude from future searches
			unset( $ruleAssoc[ $field_key ] ); //row done, no need to work on it again
		}

	}
}
echo "Fields matched: \n";
print_r( $fields_ok ); //all found fields as $field_pos => $col_name

$part2 = 1; //This must be 1, as 0*anything = 0. I got this wrong SO may times in my 28 years
foreach ( $fields_ok as $pos => $name ) {

	if ( preg_match( '/^departure/', $name ) ) { //easiest regex AoC 2020 as of today
		$part2 *= $my[ $pos ];
	}

}

echo "Part 2 solution: $part2 \n";

$end = microtime( true );
echo "Time total= " . ( $end - $start ); //on macbook pro 13 i5 2019 php8+jit = ~0.031s , vanilla php8 = ~0.035s
print_r( opcache_get_status()['jit'] ); //fancy php8 stuff
//kthxbye