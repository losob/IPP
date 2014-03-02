<?php

function array_change_key(&$array, $old_key, $new_key)
{
    $array[$new_key] = $array[$old_key];
    unset($array[$old_key]);
    return;
}

function print_help(){
	print "Napoveda:\n\n";
	print "--help: vypise tuto napovedu\n\n";
	print "--input=filename: zadany vstupni JSON soubor v kodovani UTF-8\n\n";
	print "--output=filename: textovy vystupni XML soubor v kodovani UTF-8 s obsahem prevedenym ze vstupniho souboru\n\n";
	print "-h=subst: ve jmene elementu odvozenem z dvojice jmeno-hodnota nahradte kazdy nepovoleny znak ve jmene XML znacky retezcem subst. Implicitne (i pri nezadanem parametru -h) uvazujte nahrazovani znakem pomlcka (-). Vznikne-li po nahrazeni invalidni jmeno XML elementu, skoncete s chybou a navratovym kodem 51.\n\n";
	print "-n: negenerovat XML hlavicku na vystup skriptu (vhodne napriklad v pripade kombinovani vice vysledku)\n\n";
	print "-r=root-element: jmeno paroveho korenoveho elementu obalujici vysledek. Pokud nebude zadan, tak se vysledek neobaluje korenovym elementem, ac to potencionalne porusuje validitu XML (skript neskonci s chybou). Zadani retezce root-element vedouciho na nevalidni XML znacku ukonci skript s chybou a navratovym kodem 50 (nevalidni znaky nenahrazujte).\n\n";
	print "--array-name=array-element: tento parametr umozni prejmenovat element obalujici pole z implicitni hodnoty array na array-element. Zadani retezce array-element vedouciho na nevalidni XML znacku ukonci skript s chybou a navratovym kodem 50 (nevalidni znaky nenahrazujte).\n\n";
	print "--item-name=item-element: analogicky, timto parametrem lze zmenit jmeno elementu pro prvky pole (implicitni hodnota je item). Zadani retezce item-element vedouciho na nevalidni XML znacku ukonci skript s chybou a navratovym kodem 50 (nevalidni znaky nenahrazujte).\n\n";
	print "-s: hodnoty (v dvojici i v poli) typu string budou transformovany na textove elementy misto atributu.\n\n";
	print "-i: hodnoty (v dvojici i v poli) typu number budou transformovany na textove elementy misto atributu.\n\n";
	print "-l: hodnoty literalu (true, false, null) budou transformovany na elementy <true/>, <false/> a <null/> misto na atributy.\n\n";
	print "-c: tento prepinac oproti implicitnimu chovani aktivuje preklad problematickych znaku. Pro XML problematicke znaky s UTF-8 kodem mensim jak 128 ve vstupnich retezcovych hodnotach (ve dvojicich i polich) konvertujte na odpovidajici zapisy v XML pomoci metaznaku & (napr. &amp;, &lt;, &gt; atd.). Ostatni problematicke znaky konvertovat nemusite.\n\n";
	print "-a, --array-size: u pole bude doplnen atribut size s uvedenim poctu prvku v tomto poli.\n\n";
	print "-t, --index-items: ke kazdemu prvku pole bude pridan atribut index s urcenim indexu prvku v tomto poli (cislovani zacina od 1, pokud neni parametrem --start urceno jinak).\n\n";
	print "--start=n: inicializace inkrementalniho citace pro indexaci prvku pole (nutno kombinovat s parametrem --index-items, jinak chyba 1) na zadane kladne cele cislo n vcetne nuly (implicitne n = 1).\n\n";
}




$shortoptions = "";
$shortoptions .= "h:";	// nahrazeni nepovoleneho znaku -
$shortoptions .= "n";	// negenerovat hlavicku XML
$shortoptions .= "r:";	// nazev korenoveho elementu - nenahrazovat nevalidni znaky!!
$shortoptions .= "s";	// nechapu
$shortoptions .= "i";	// nechapu
$shortoptions .= "l";	// nechapu
$shortoptions .= "c";	// preklada problematicke znaky (nizni nez 128)
$shortoptions .= "a";	// u poli doplnen atribut size 
$shortoptions .= "t";	// kazdy prvem ma atribut index (kolikaty v poli)

$longoptions  = array(
	"help",			// vypise napovedu
	"input:",		// vstupni soubor, jinak stdin
	"output:",		// vystupni soubor, jinak stdout
	"array-name:",	// implicitni 'array' nahradi hodnotou - nenahrazovat nevalidni znaky!!
	"item-name:",	// implicitni 'item' nahradi hodnotou - nenahrazovat nevalidni znaky!!
	"array-size",	// u poli doplnen atribut size 
	"index-items",	// kazdy prvem ma atribut index (kolikaty v poli)
	"start:",		// inicializace citace - v kombinaci s index-items!!
);


$root = ""; // promenna pro prepinac r

$options = getopt($shortoptions, $longoptions);

#var_dump($options); #vypise strukturu $options
#if (array_key_exists(co, kde))

if (count($options) != (count($argv)-1)) {	// odecten nazev souboru
	echo "Spatne zadane prepinace. Pro napovedu: --help\n";
	exit(1);		// neni nahodou cistejsi return?
}



foreach ($options as $key => $value) {
	#$argument = explode('=', $value);
	switch ($key) {
		case 'help':		// vypise napovedu
			if (count($argv) != 2) {	// prepinac --help musi byt osamocen
				echo "Pro napovedu prepinac --help\n";
				exit(1);
			}
			print_help();
			exit(0);
			break;

		case 'input':		// vstupni soubor, jinak stdin
			echo "input\n";
			if (!file_exists($value)) {
				echo "Vstupni soubor neexistuje!\n";
				exit(2);
			}
			$obsah = file_get_contents($value);
			break;
		case 'output':		// vystupni soubor, jinak stdout
			echo "output\n";
			$output = fopen($value, 'w');
			break;
		case 'h':			// volitelne =; nahrazeni nepovoleneho znaku -
			echo "-h=".$value."\n";
			break;
		case 'n':			// negenerovat hlavicku XML
			echo "-n\n";
			break;
		case 'r':			// nazev korenoveho elementu - nenahrazovat nevalidni znaky!!
			echo "-r=".$value."\n";
			break;
		case 'array-name':	// implicitni 'array' nahradi hodnotou - nenahrazovat nevalidni znaky!!
			echo "--array-name=".$value."\n";
			break;
		case 'item-name':	// implicitni 'item' nahradi hodnotou - nenahrazovat nevalidni znaky!!
			echo "--item-name=".$value."\n";
			break;
		case 's':
			echo "-s\n";
			break;
		case 'i':
			echo "-i\n";
			break;
		case 'l':
			echo "-l\n";
			break;
		case 'c':				// preklada problematicke znaky (nizni nez 128)
			echo "-c\n";
			break;
		case 'array-size':		// u poli doplnen atribut size 
			echo "--array-size\n";
			array_change_key($options, $key, 'a');
			break;
		case 'a':		// u poli doplnen atribut size 
			echo "--array-size\n";
			break;
		case 'index-items':		// kazdy prvem ma atribut index (kolikaty v poli)
			echo "--index-items\n";
			array_change_key($options, $key, 't');
		case 't':
			if (!array_key_exists('start', $options)){
				$options['start'] = 0;
			}
			break;
		case 'start':			// inicializace citace - v kombinaci s index-items!!
			echo "--start=".$value."\n";
			if (!array_key_exists('index-items', $options) and !array_key_exists('t', $options)){
				error_log("Pri aktivaci --start=N musi byt zadan i prepinac --index-items!");
				exit(1);
			}
			break;

		default:
			echo "Sem se snad nikdy nedostanu.\n";
			break;
	}
}

if(!array_key_exists('output', $options)){
	$output = fopen('php://output', 'w');
}
if(!array_key_exists('input', $options)){
	$obsah = file_get_contents('php://stdin');
}
if(!array_key_exists('n', $options)){
	fwrite($output, '<?xml version="1.0" encoding="UTF-8"?>'."\n");
}

function vypis_hodnotu($value, $options = []){
	switch (gettype($value)) {
		case 'string':
		case 'integer':
		case 'double':
			return (array_key_exists('c', $options)) ? ChangeName($value) : $value;
			break;
		case 'boolean':
			return ($value == true) ? "true" : "false";
			break;
		case 'NULL':
			return "null";
			break;
		default:
			echo "Error : nemozne se stalo moznym.";
			break;
	}
}
function ChangeName($text){
	return strtr($text, array('&' => '&amp;', '<' => '&lt;',
		'>' => '&gt;', '"' => '&quot;', '\'' => '&apos;'));
}
function CheckValidyName(&$text, $options){
	$substr = '-';
	if (array_key_exists('h', $options)) {
		$substr = $options['h'];
	}
	echo "\t$text\n";
	#$text = preg_filter("/[|\\|\"|>|<|&|]/", $substr, $text);
	echo preg_filter('/&/', '-', "firstName"), "\n";
	$text = preg_filter("/&/", $substr, $text);
	#return ($text[0] == $substr) ? true : false;
}

function ArrToXml($array, &$options, $depth = 0){	// je nuten options predavat odkazem?
	$odsazeni = '';
	$return = '';
	if($depth == 0 and array_key_exists('r', $options)){		// zpracovani parametru -r=ROOT - korenovy element
		$return .= "<{$options['r']}>\n";
		$depth = 1;	
	}
	$array_name = "array";
	$item_name = "item";
	if (array_key_exists('array-name', $options))
		$array_name = $options['array-name'];
	if (array_key_exists('item-name', $options))
		$item_name = $options['item-name'];



	for($i = 0; $i < $depth; $i++)
		$odsazeni .= "\t";
	$index_items = "";
	if (is_array($array)) 
			if (array_key_exists('0', $array)){
				if (array_key_exists('a', $options))
					$return .= $odsazeni."<{$array_name} size=\"".count($array)."\">\n";
				else
					$return .= $odsazeni."<{$array_name}>\n";
				$odsazeni .= "\t";
			}
		foreach($array as $key => $value){
		#	echo $key."\n";
			CheckValidyName($key, $options);
		#	echo $key."\n";
			if(is_array($value)){
		#echo "\nklic: ".$key."; hodnota: ".@$value."\n";
		#echo vypis_hodnotu(is_numeric($key), $options)."; {$array_name}\n";
				if (is_numeric($key)) {
					$index_items = "";
					if (array_key_exists('t', $options)){
						$index_items = " index=\"".$options['start']."\"";
						$options['start'] += 1;
					}
					$return .= $odsazeni."<{$item_name}{$index_items}>\n";
					$return .= ArrToXml($value, $options, $depth + 2);
					$return .= $odsazeni."</{$item_name}>\n";
				}
				elseif (array_key_exists('0', $value)) {
				if (array_key_exists('a', $options) and ($array_name == $key)) {
					$key .= " size=\"".count($value)."\"";
				}
					$return .= $odsazeni."<{$key}>\n";
#					$return .= $odsazeni."\t<{$array_name}>\n";
					$return .= ArrToXml($value, $options, $depth + 1);
#					$return .= $odsazeni."\t</{$array_name}>\n";
					$return .= $odsazeni."</{$key}>\n";
				}
				else {
					$return .= $odsazeni."<{$key}>"."\n";
					$return .= ArrToXml($value, $options, $depth + 1);
					$return .= $odsazeni."</{$key}>\n";
				}
			}
			else{
				$key = (is_int($key)) ? "{$item_name}" : $key;
				if (array_key_exists('t', $options) and ($item_name == $key)){
					$index_items = " index=\"".$options['start']."\"";
					$options['start'] += 1;
				}
	#	echo "{$key}{$index_items}\n";
				if ((gettype($value) == 'string' and array_key_exists('s',$options)) 
					or (is_numeric($value) and array_key_exists('i', $options))) {
					$return .= $odsazeni."<{$key}{$index_items}>\n";
					$return .= $odsazeni."\t".vypis_hodnotu($value, $options)."\n";
					$return .= $odsazeni."</{$key}>\n";
				}
				elseif(array_key_exists('l', $options) and (gettype($value) == 'boolean' or gettype($value) == 'NULL')) {
					$return .= $odsazeni."<{$key}{$index_items}>\n";
					$return .= $odsazeni."\t<".vypis_hodnotu($value, $options)."/>\n";
					$return .= $odsazeni."</{$key}>\n";
				}
				else{
					$return .= $odsazeni."<{$key}{$index_items} value=\"";		// nutne pro vypsani hodnot false, null
					$return .= vypis_hodnotu($value,$options);
					$return .= "\"/>\n";
				}
			}
			#echo "\nklic: ".$key."\nhodnota: ".@$value."\n";
		}
		if (array_key_exists('0', $array)){
			$return .= substr($odsazeni,1)."</{$array_name}>\n";
		}
		if($depth == 1 and array_key_exists('r', $options)) {
			$return .= "</{$options['r']}>\n";
		}
	return $return;
}

$data = json_decode($obsah, true);
#var_dump($data);

fwrite($output, ArrToXml($data, $options));
fclose($output);
?>