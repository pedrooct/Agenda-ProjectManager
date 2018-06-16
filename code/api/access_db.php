<?php

class AccessDB
{
	private $query;

	public function __construct($query){
		$this->query=$query;
	}

	// permite efetuar pesquisas na DB
	public function procurar(){
		$conn = mysqli_connect("localhost","root","root","lpi");
		if(!$conn)
		{
			die('Error: ' . mysqli_connect_error());
		}
		$aux = mysqli_query($conn,$this->getQuery());
		mysqli_close($conn);
		return $aux;
	}
	// permite adicionar entradas na DB
	public function adicionar(){
		$conn = mysqli_connect("localhost","root","root","lpi");
		if(!$conn)
		{
			die('Error: ' . mysqli_connect_error());
		}
		$aux = mysqli_query($conn,$this->getQuery());

		if(empty($conn->error)) {
			$id=$conn->insert_id;
			mysqli_close($conn);
			return $id;
		}
		mysqli_close($conn);
		return false;
	}
	//permite atualizar entradas na DB
	public function atualizar(){
		$conn = mysqli_connect("localhost","root","root","lpi");
		if(!$conn)
		{
			die('Error: ' . mysqli_connect_error());
		}
		$aux = mysqli_query($conn,$this->getQuery());
		if(empty($conn->error)) {
			mysqli_close($conn);
			return $aux;
		}
		mysqli_close($conn);
		return false;
	}
	//permite remover entradas da DB
	public function remover(){
		$conn = mysqli_connect("localhost","root","root","lpi");
		if(!$conn)
		{
			die('Error: ' . mysqli_connect_error());
		}
		$aux = mysqli_query($conn,$this->getQuery());
		mysqli_close($conn);
		return $aux;
	}
	//permite efetuar pesquisas na DB ou na Cache
	public function procurar_cache($query){
		return mysqli_query($conn,$query);

	}

	public function getQuery(){
		return $this->query;
	}

	public function setQuery($query){
		$this->query=$query;
	}
}

/**
* Class para aceder á Memcached e serviço cache
*/
class AcessCached
{
	public function saveCacheMem($code,$response)
	{
		$mem = new Memcached();
		$mem->addServer("localhost", 11211);
		$mem->set($code,$response,1800);
		return true;
	}
	public function getCacheMem($code)
	{
		$mem = new Memcached();
		$mem->addServer("localhost", 11211);
		$aux=$mem->get($code);
		return $aux;
	}
	public function saveCodeMemcached($code)
	{
		$mem = new Memcached();
		$mem->addServer("localhost", 11211);
		$mem->set($code,TRUE,28800);
		return "ok";
	}
	public function getCodeMemcached($code)
	{
		$mem = new Memcached();
		$mem->addServer("localhost", 11211);
		$aux=$mem->get($code);
		$mem->delete($code);
		return $aux;
	}
	public function getMemcached($code)
	{
		$mem = new Memcached();
		$mem->addServer("localhost", 11211);
		$aux=$mem->get($code);
		return $aux;
	}
}
class AccessMongo
{
	//
	public function saveHorario($data,$perfil_id)
	{
		$data=json_encode($data);
		$client= new MongoDB\Driver\Manager();
		$write = new MongoDB\Driver\BulkWrite;
		$MongoID= new MongoDB\BSON\ObjectId;
		$document = ['pid'=>$perfil_id,'_id' => $MongoID, 'horario' => $data];
		$write->insert($document);
		$result=$client->executeBulkWrite('lpimongo.horario', $write);
		return $MongoID;
	}
	public function updateHorario($data,$mongoID)
	{
		$data=json_encode($data);
		$client= new MongoDB\Driver\Manager();
		$write = new MongoDB\Driver\BulkWrite;
		$MongoID= new MongoDB\BSON\ObjectId($mongoID['horario_livre']);
		$write->update(['_id'=>$MongoID],
		['$set'=>['horario'=>$data]],
		['multi'=>true,'upsert'=>false]);
		$resultado=$client->executeBulkWrite("lpimongo.horario",$write);
		return true;
	}
	public function getHorarioMongo($mongoId)
	{
		$client = new MongoDB\Driver\Manager();
		$id = new \MongoDB\BSON\ObjectId($mongoId);
		$filtros = ['_id'=> $id];
		$opcoes = ['projection' => ['_id' => 0]];
		$query = new MongoDB\Driver\Query($filtros, $opcoes);
		$pacote = $client->executeQuery('lpimongo.horario', $query);
		foreach ($pacote as $r) {
		  return $r->horario;
		}
	}

	public function insertDoc($nome_ficheiro,$ficheiro,$tipo,$notas,$perfil_id){
		$ficheiro= base64_encode(file_get_contents($ficheiro));
		$client= new MongoDB\Driver\Manager();
		$write = new MongoDB\Driver\BulkWrite;
		$MongoID= new MongoDB\BSON\ObjectId;
		$document = ['pid'=>$perfil_id,'_id' => $MongoID,'nome'=>$nome_ficheiro ,'ficheiro' => $ficheiro,'tipo'=>$tipo,'notas'=> $notas];
		$write->insert($document);
		$result=$client->executeBulkWrite('lpimongo.ficheiro', $write);
		return $MongoID;
	}

	public function getDocumentoMongo($mongoId)
	{
		$client = new MongoDB\Driver\Manager();
		$id = new \MongoDB\BSON\ObjectId($mongoId);
		$filtros = ['_id'=> $id];
		$opcoes = ['projection' => ['_id' => 0] ];
		$query = new MongoDB\Driver\Query($filtros, $opcoes);
		$pacote = $client->executeQuery('lpimongo.ficheiro', $query);
		foreach ($pacote as $r) {
		  return $r;
		}
	}
}

?>
