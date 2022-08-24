<?php

/*

Dieses Script ist noch in einem experimentellen Zustand und ist noch nicht ganz Fehlerfrei.

This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

In jurisdictions that recognize copyright laws, the author or authors
of this software dedicate any and all copyright interest in the
software to the public domain. We make this dedication for the benefit
of the public at large and to the detriment of our heirs and
successors. We intend this dedication to be an overt act of
relinquishment in perpetuity of all present and future rights to this
software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <https://unlicense.org>

*/

session_start();
if(isset($_GET['restart'])){session_destroy();header("Location:?");exit;}
?>
<head>
	<title>Ratespiel</title>
		<meta name="robots" content="noindex, nofollow, noarchive, nosnippet, max-image-preview:none, notranslate" />
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
html {padding:1em;}
.output {letter-spacing: 1em;padding:0em;line-height:1em;margin-bottom:-0.5em; }
span {margin-right:0.2em;}
</style>
</head>
<?php

if(isset($_GET['generieren']) or(!is_file("Worttabelle.txt"))){
	// WÖRTER GENERIEREN
	$wortliste = "https://raw.githubusercontent.com/dys2p/wordlists-de/main/de-7776-v1.txt";
	$liste = file($wortliste);
	foreach($liste as $zeile)
	{
		$zeile = trim($zeile);
		if(strlen($zeile) == 5)
		{
			$woerter[] = $zeile;
		}
	}
	$woerter = implode("\n",$woerter);
	file_put_contents("Worttabelle.txt",$woerter);
}

if(!isset($_SESSION['gesuchtes_wort']))
{
$liste = file("Worttabelle.txt");
$_SESSION['gesuchtes_wort'] = trim(strtoupper($liste[mt_rand(0,count($liste)-1)]));	
}

//echo $_SESSION['gesuchtes_wort'];

$gesuchtes_wort_array = str_split($_SESSION['gesuchtes_wort']);
$preview= '<span style="background-color:lightgreen;">'.$gesuchtes_wort_array[0].'</span><span style="background-color:lightgrey;" >-</span><span style="background-color:lightgrey;" >-</span><span style="background-color:lightgrey;" >-</span><span style="background-color:lightgrey;" >-</span>';


if(isset($_POST['eingabe']))
{
	$eingabe = strtoupper(trim(strip_tags($_POST['eingabe'])));
	$eingabe_array =str_split($eingabe);
	
	$counter = -1;
	$output = "";
	$anzahl = 0;
	foreach($eingabe_array as $buchstabe)
	{
		$counter++;
		if($buchstabe == "*")
		{
			$eingabe_array[$counter] = $gesuchtes_wort_array[$counter];
			$output .=  '<span style="background-color:lightgreen;" >'.$gesuchtes_wort_array[$counter].'</span>';
		}
		elseif(!in_array($buchstabe,$gesuchtes_wort_array))
		{
			$output .=  '<span style="background-color:lightgrey;" >'.$buchstabe.'</span>';
		}
		else 
		{
			
		if($buchstabe == $gesuchtes_wort_array[$counter])
		{
			//unset($gesuchtes_wort_array[$counter]);
			$output .= '<span style="background-color:lightgreen;">'.$buchstabe.'</span>';
			$anzahl++;
			if($anzahl == strlen($_SESSION['gesuchtes_wort'])){$gewonnen="1";break;}
		}
		else {
			$output .= '<span style="background-color:orange;">'.$buchstabe.'</span>';
		}
	}
	
	
}
if(isset($gewonnen))
{
	echo "Wort: <b>".$_SESSION['gesuchtes_wort']."</b><br>";
	echo 'Gewonnen!<hr>
	<a href="?restart"><button autofocus>neues Wort</button></a>'; session_destroy();exit;
}


$_SESSION['output'][] =  $output;
}

echo  $preview;

if(!empty($_SESSION['output'])){
	$counter = 0;
	foreach($_SESSION['output'] as $output)
	{
		$counter++;
		echo "<p class='output'>".$output." [".$counter."]<p>";
	}
}

?>
<form action="?" method="POST">
<input type="text" name="eingabe" required autofocus  minlength="5" maxlength="5">
<button type="submit">check</button>
</form>
<a href="?restart"><button>neues Wort</button></a>