<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php
	require('protocolbuffers.inc.php');

/*
$varint_tests  = array(
	1   => "\x01",
	2   => "\x02",
	127 => "\x7F",
	128 => "\x80\x01",
	300 => "\xAC\x02",
);

function test_varint() {
	global $varint_tests;

	$fp = fopen('php://memory', 'r+b');
	if ($fp === false)
		exit('Unable to open stream');

	foreach ($varint_tests as $i => $enc) {

		// Write the answer into the buffer
		fseek($fp, 0, SEEK_SET);
		fwrite($fp, $enc);
		fseek($fp, 0, SEEK_SET);

		$a = Protobuf::read_varint($fp);
		if ($a != $i)
			exit("Failed to decode varint($i) got $a\n");

		$len = Protobuf::write_varint($fp, $i);
		fseek($fp, 0, SEEK_SET);
		$b = fread($fp, $len);
		if ($b != $enc)
			exit("Failed to encode varint($i)\n");

		$len = Protobuf::size_varint($i);

		echo "$i len($len) OK\n";
	}
	fclose($fp);
}
test_varint();
*/

	if ($argc > 1) {
		$test = $argv[1];
		require("$test.php");

		if ($test == 'addressbook.proto') {
			$fp = fopen('test.book', 'rb');

			$m = new tutorial_AddressBook($fp);

			var_dump($m);

			fclose($fp);

		} else if ($test == 'market.proto') {
			//$fp = fopen('market2-in-1.dec', 'rb');
			$fp = fopen('market2-in-2.dec', 'rb');
			//$fp = fopen('temp', 'rb');

			$m = new Response($fp);

			echo $m;

			//$mem = fopen('php://memory', 'wb');
			$mem = fopen('temp', 'wb');
			if ($mem === false)
				exit('Unable to open output stream');

			$s = fstat($fp);
			echo 'File size: ' . $s['size'] . "\n";
			echo 'Guested size: ' . $m->size() . "\n";
			$m->write($mem);
			echo 'Write size: ' . ftell($mem) . "\n";

			fclose($mem);
			fclose($fp);
		}
	}

?>
