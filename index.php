<?php
include_once("./Class/MyMongo.php");
include_once("./Class/MyCouch.php");
include_once("./func.php");
///////top
include_once("./View/top.html");
//////////

///////////////////////////////// MONGO

$mongo = new MyMongo();
$mongoh = $mongo->connect();
$mongodb = $mongoh->test;

echo "<b>Kolekcje</b>:<BR/><br/>";
	$mongoListColl = $mongo->listColl($mongodb);


	foreach ($mongoListColl as $col) 
	{
		echo $col."<br/>".generateOpMongo($col);
		
	}

echo <<<QUO
	<BR/><br/>
	<form enctype="multipart/form-data" action="mongoOperation.php" method="POST">
	
	Dodaj nową: <BR><input name="name" type="text"/><BR><BR>
	
	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	<input type="hidden" name="op" value="add" />
	Plik JSON: <input name="plik" type="file" /><br /><BR>
	<input type="submit" value="Dodaj plik" />

</form>

QUO;


////////////////////////////////////////

///////mid
include_once("./View/mid.html");
//////////


///////////////////////////////// COUCH


$couch = new MyCouch();
$couchh = $couch->connect();
$couchh = $couchh[0];



//$couch->createDB($couchh,'test');
echo "<b>Dokumenty</b>:<BR/><br/>";

$couchDocs=json_decode($couch->getAllDocs($couchh,"test"));
$couchDocs = get_object_vars($couchDocs);

//print_r($couchDocs);

for($i=0; $i<sizeof($couchDocs['rows']);$i++)
{
	$couchDocObj = get_object_vars($couchDocs['rows'][$i]);
	$rev = get_object_vars($couchDocObj['value']);
	$rev = $rev['rev'];
	
	echo $couchDocObj['key']."<br/>".generateOpCouch($couchDocObj['key'],$rev);
}

echo <<<QUO
	<BR/><br/>
	<form enctype="multipart/form-data" action="couchOperation.php" method="POST">
	
	Dodaj nowy: <BR><input name="name" type="text"/><BR><BR>
	
	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	<input type="hidden" name="op" value="add" />
	Plik JSON: <input name="plik" type="file" /><br /><BR>
	<input type="submit" value="Dodaj plik" />

</form>

QUO;


////////////////////////////////////////

///////bottom
include_once("./View/bottom.html");
//////////

?>
