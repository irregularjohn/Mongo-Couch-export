<?php
include_once("./Class/MyMongo.php");
include_once("./Class/MyCouch.php");
include_once("./func.php");


if(isset($_GET['op'])) { $op=$_GET['op'];}
if(isset($_GET['name'])) { $name=$_GET['name'];}

$couch = new MyCouch();
$couchh = $couch->connect();
$couchh = $couchh[0];

if(isset($_POST['op']))
{ 
	$op=$_POST['op'];
	
	if(isset($_POST['name'])) { $name=$_POST['name'];}
	
	if($op="add")
	{
		$target_path = "pliki/";

		$target_path = $target_path . basename( $_FILES['plik']['name']); 
		
		
		if(move_uploaded_file($_FILES['plik']['tmp_name'], $target_path))
		{	
			echo "Dodano ".  basename( $_FILES['plik']['name']). " jako dokument ".$name.". <BR/> <a href='index.php'>Powrót</a>";
		} 
		else
		{
			echo "There was an error uploading the file, please try again!";
		}
		
		$file=file_get_contents($target_path,"r");

		$fromjson = json_decode($file);
		
		
		
		
		$couch->createDoc($couchh,"test",$name,$file);
		
	}
}
else
{

	
	if($op=="show")
	{				
		$bla = $couch->getDoc($couchh,'test',$name);

		print_r(indent($bla));

		
	}
	else if($op=="del")
	{
		if(isset($_GET['rev'])) { $rev=$_GET['rev'];}
		
		$bla = $couch->delDoc($couchh,'test',$name,$rev);
		
		//echo $bla;
		echo "Usunięto dokument. <BR/> <a href='index.php'>Powrót</a>";
		
	}
	else if($op=="exp")
	{
		$output = $couch->getDoc($couchh,'test',$name);
		$output=json_decode($output);
		unset($output->_id);
		unset($output->_rev);
		//print_r($output);
		$output=json_encode($output);
		
		$output=indent(str_replace('\\/', '/',$output));
		
		$filename = $name.".json";
		$file = fopen($filename, 'w') or die("can't open file");
		
		fwrite($file, $output);
		
		fclose($file);
		
		echo "Zapisano do pliku <a href='".$filename."'>".$filename."</a><BR/><a href='index.php'>Powrót</a>";
	
	}
	else if($op=="2Mongo")
	{
		$output = $couch->getDoc($couchh,'test',$name);
		$output=json_decode($output);
		unset($output->_id);
		unset($output->_rev);
		//print_r($output);
		//$output=json_encode($output);
		
		//$output=indent(str_replace('\\/', '/',$output));
		//echo $output;
		//////////
		
		$mongo = new MyMongo();
		$mongoh = $mongo->connect();
		$mongodb = $mongoh->test;
		
		$mongo->createColl($mongodb,$name);
		$coll = $mongo->selectColl($mongodb,$name);
		
		 $mongo->setColl($coll,$output);
		 
		 	echo "Wyeksportowano do MongoDB.<BR/><a href='index.php'>Powrót</a>";
		 
	}
}

?>
