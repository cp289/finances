<?php

// for debugging...
function pp($var, $die=true) {
	print('<pre>');
	print_r($var);
	print('</pre>');
	if ($die) die();
}
