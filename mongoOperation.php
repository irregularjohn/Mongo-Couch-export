<?php
include_once("./Class/MyMongo.php");
include_once("./Class/MyCouch.php");
include_once("./func.php");


if(isset($_GET['op'])) { $op=$_GET['op'];}
if(isset($_GET['name'])) { $name=$_GET['name'];}

		$mongo = new MyMongo();
		$mongoh = $mongo->connect();
		$mongodb = $mongoh->test;

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
			echo "Dodano kolekcję ".  basename( $_FILES['plik']['name']). ". <BR/> <a href='index.php'>Powrót</a>";
		} 
		else
		{
			echo "There was an error uploading the file, please try again!";
		}
		
		$file=file_get_contents($target_path,"r");

		$fromjson = json_decode($file);
		
		
		
		
		$mongo->createColl($mongodb,$name);
		$coll = $mongo->selectColl($mongodb,$name);
		
		 $mongo->setColl($coll,$fromjson);
		
	}
}
else
{
	$name=explode(".",$name);
	$name=$name[1];
	
	if($op=="show")
	{
		
		
		$coll = $mongo->selectColl($mongodb,$name);
		$cursor = $coll->find();
		
		foreach ($cursor as $bla)
		{
		print_r($bla);
		}
		
	}
	else if($op=="del")
	{
		$coll = $mongo->selectColl($mongodb,$name);
		$cursor = $coll->drop();
		echo "Usunięto kolekcję <BR/> <a href='index.php'>Powrót</a>";
	}
	else if($op=="exp")
	{
		$coll = $mongo->selectColl($mongodb,$name);
		$cursor = $coll->find();
		
		$output="";
		
		foreach ($cursor as $bla)
		{
			unset($bla["_id"]);

			$output=$output.json_encode($bla);

		}
		
		$output=indent(str_replace('\\/', '/',$output));
		
		$filename = $name.".json";
		$file = fopen($filename, 'w') or die("can't open file");
		
		fwrite($file, $output);
		
		fclose($file);
		
		echo "Zapisano do pliku <a href='".$filename."'>".$filename."</a><BR/><a href='index.php'>Powrót</a>";
	
	}
	else if($op=="2Couch")
	{

		$coll = $mongo->selectColl($mongodb,$name);
		$cursor = $coll->find();
		
		$output="";
		
		$count=0;
		
		
		foreach ($cursor as $bla)
		{
			unset($bla["_id"]);

			$output=$output.json_encode($bla).",";

			$count++;
		}
		
		$output=substr($output, 0, -1);
		//echo $output;
		if($count>1)
		{
			$output="[".$output."]";
		}
		
		$output=indent(str_replace('\\/', '/',$output));
		
		//////////////
			$couch = new MyCouch();
			$couchh = $couch->connect();
			$couchh = $couchh[0];
			
			//echo $output;
			
			$odp = $couch->createDoc($couchh,"test",$name,$output);
			
			//echo $odp;
			
			echo "Wyeksportowano do CouchDB.<BR/><a href='index.php'>Powrót</a>";
			
		/*	$odp = json_decode($odp);
			if($odp->error=='conflict')
			{
				//print_r(json_decode($couchh->send("GET", "/test/_changes"))->results); 
				$changes = json_decode($couchh->send("GET", "/test/_changes"))->results;
				for($k=0;$k<sizeof($changes);$k++)
				{
					if($changes[$k]->id==$name)
					{
						$upRev = $changes[$k]->changes[0]->rev;
						
						$output=json_decode($output);
						print_r($output);
						
						////////////// NIEISTOTNE///////////////
						
						
					}
				}
			}
			
			*/
			//echo "Dodano kolekcję ".$name." do CouchDB.<BR/><a href='index.php'>Powrót</a>";
			
		//////////////
		
	}
}

?>
