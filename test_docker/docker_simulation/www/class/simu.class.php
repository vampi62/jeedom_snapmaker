<?php
class Simu
{
	private $bdd;
	private $token;
	
	private $reponseConnection;
	public function __construct($token, $bdd)
	{
		$this->bdd = $bdd;
		$this->token = $token;
		if ($_GET['token'] != "") {
			$reponseConnection = $bdd->prepare('SELECT * FROM apikey WHERE keyg = :user');
			$reponseConnection->execute(array(
				'user' => $token,
			));


			$reponseConnection = $reponseConnection->fetch(PDO::FETCH_ASSOC);
			$this->reponseConnection = $reponseConnection;
		}
		else
		{
			$this->reponseConnection = array();
		}
	}
	
	public function key_exist()
	{
		if(!empty($this->reponseConnection))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function key_valide()
	{
		if($this->reponseConnection["valide"])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function key_connect()
	{
		if($this->reponseConnection["connect"])
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function setnewkey()
	{
		$newkey = $this->gen_api_key();
		$this->setkeydb($newkey);
		$this->token = $newkey;
		$this->set_key_connect(1);
		return $this->token;
	}

	public function connectkey()
	{
		$this->set_key_connect(1);
		return $this->token;
	}
	public function getstatus()
	{
		$this->set_key_connect(1);
		return $this->token;
	}
	public function getenclosure()
	{
		$this->set_key_connect(1);
		return $this->token;
	}

	private function gen_api_key()
	{
		$alphabet = '4A1B81CD4EF8G3HI5JK5L6M7N7OP0QR9S3T6UVW02X9YZ2';
		$api_key = '';
		$alphaLength = strlen($alphabet) - 1;
		for ($i = 0; $i < 12; $i++)
		{
			$n = mt_rand(0, $alphaLength);
			$api_key .= $alphabet[$n];
		}
		return $api_key;
	}

	private function set_key_connect($con)
	{
		$date = date('Y-m-d H:i:s');
		$req = $this->bdd->prepare('UPDATE apikey SET connect = :connect, time = :date WHERE keyg = :keyg');
		$req->execute(array(
			'connect' => $con,
			'keyg' => $this->token,
			'date' => $date
		));
	}

	private function setkeydb($key)
	{
		$date = date('Y-m-d H:i:s');
		$req = $this->bdd->prepare('INSERT INTO apikey(keyg, valide, connect, time) VALUES(:keyg, 0, 1, :date)');
		$req->execute(array(
			'keyg' => $key,
			'date' => $date
		));
	}

	public function setlogdb($code)
	{
		$date = date("Y-m-d H:i:s");
		$req = $this->bdd->prepare('INSERT INTO log(date, status, id_apikey) VALUES(:date, :status, :id_apikey)');
		$req->execute(array(
			'date' => $date,
			'id_apikey' => $this->token,
			'status' => $code
		));
	}
}
?>
