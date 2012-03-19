<?php

class MyMongo
{
			function connect()
			{
				$m = new Mongo();
				
				return $m;
			}
			
			function selectDB($m,$name)
			{
				$db = $m->$name;
				
				return $db;
			}
			
			function createColl($db,$col_name)
			{
				$db->createCollection($col_name);
			}
			
			function selectColl($db,$col_name)
			{
				$collection = $db->$col_name;
				
				return $collection;
			}
			
			function setColl($collection,$data)
			{
				$collection->save($data);
			}
			
			function listColl($db)
			{
				$list = $db->listCollections();
				
				return $list;
			}
		
}

?>
