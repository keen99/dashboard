<?php


function createWildcardPattern($input = array()) {
	$verbose = false;
#	if(defined('STDIN') and $argc > 1 and $argv[1] == "-v")
#		$verbose = true;
	
	$wrapper = array();
	foreach ($input as $string) {
		$ndx = count(explode('.', $string));
		if (!array_key_exists($ndx, $wrapper) or count($wrapper[$ndx]) == 0) {
			$tmp_array = array();
		} else {
			$tmp_array = $wrapper[$ndx];
		}
		array_push($tmp_array,$string);
		$wrapper[$ndx] = $tmp_array;
	}
	
	$output = array();
	foreach ($wrapper as $input) {
		if ($verbose) {
			print_r($input);
		}
		
		$match = null;
		foreach ($input as $string) {
			if ($verbose) {
				echo "match: $match\n";
				echo "string: $string\n";
			}
			if (strlen($match) == 0) {
				if ($verbose) {
					echo "match is empty. setting to '$string'\n\n";
				}
				$match = $string;
				continue;
			}
		
			$match_ndx = 0;
			$string_ndx = 0;
			$newmatch = null;
			while (true) {
				if ($string_ndx >= strlen($string))
					break;
				if ($match_ndx >= strlen($match))
					break;
		
				if ($verbose) {
					echo $string[$string_ndx] . " " . $match[$match_ndx];
				}
				if ($string[$string_ndx] == $match[$match_ndx]) {
					if ($verbose) {
						echo " - match";
					}
					$newmatch .= $string[$string_ndx];
					if ($verbose) {
						echo " $newmatch\n";
					}
				} elseif ($match[$match_ndx] == '*') {
					if ($verbose) {
						echo " - found *, checking next match char";
					}
					if ($newmatch[strlen($newmatch) - 1] != '*')
						$newmatch .= '*';
					$match_ndx++;
					if ($verbose) {
						echo " $newmatch\n";
					}
					continue;
				} else {
					if ($verbose) {
						echo " - no match\n";
					}
		
					for ($i = 1; ($i + $string_ndx) < strlen($string); $i++) {
						if ($verbose) {
							echo $string[$i + $string_ndx] . " $match[$match_ndx]\n";
						}
						if ($string[$i + $string_ndx] == $match[$match_ndx] or $string[$i + $string_ndx] == '.' or $match[$match_ndx] == '.')
							break;
					}
	
					for ($j = 1; ($j + $match_ndx) < strlen($match); $j++) {
						if ($verbose) {
							echo "$string[$string_ndx] " . $match[$j + $match_ndx] . "\n";
						}
						if ($match[$j + $match_ndx] == $string[$string_ndx] or $match[$j + $match_ndx] == '.' or $string[$string_ndx] == '.')
							break;
					}
	
					if ($verbose) {
						echo "string_ndx: $string_ndx\n";
						echo "match_ndx: $match_ndx\n";
						echo "i: $i\n";
						echo "j: $j\n";
					}
		
					if (($i + $string_ndx) == strlen($string) and ($j + $match_ndx) == strlen($match)) {
						if ($verbose) {
							echo "adding * to new match\n";
						}
						if ($newmatch[strlen($newmatch) - 1] != '*')
							$newmatch .= '*';
						if ($verbose) {
							echo "newmatch: $newmatch\n";
						}
					} elseif ($i > $j) {
						if ($verbose) {
							echo "adding * to new match\n";
						}
						if ($newmatch[strlen($newmatch) - 1] != '*')
							$newmatch .= '*';
						if ($verbose) {
							echo "newmatch: $newmatch\n";
							echo "setting match_ndx to " . ($j + $match_ndx) . " " . $match[$j + $match_ndx] . "\n";
						}
						$match_ndx = ($j + $match_ndx);
						continue;
					} else {
						if ($verbose) {
							echo "adding * to new match\n";
						}
						if ($newmatch[strlen($newmatch) - 1] != '*')
							$newmatch .= '*';
						if ($verbose) {
							echo "newmatch: $newmatch\n";
							echo "setting string_ndx to " . ($i + $string_ndx) . " " . $string[$i + $string_ndx] . "\n";
						}
						$string_ndx = ($i + $string_ndx);
						continue;
					}
				}
				$match_ndx++;
				$string_ndx++;
			}
			$match = $newmatch;
			if ($verbose) {
				echo "now matching $match\n\n";
			}
		}
		if ($verbose) {
			echo "Match: $match\n\n";
		}
		array_push($output, $match);
	}
	return $output;
}

