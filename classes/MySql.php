<?

class MySql{
	
	private static $pdo;
	
	public static function conectar(){
		if(self::$pdo == null){
			try{
				self::$pdo = new PDO('mysql:host='.HOST.';dbname='.DATABASE,USER,PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"));
				self::$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			}catch(Exception $e){
				echo '<h2>Erro ao conectar com banco de dados!</h2>';
				die("<center>Tivemos um erro de conexão com o banco de dados!!<br> Entre em contato com administrador.<p><b>Erro:</b> ".$e->getMessage()."</center>");
			}
		}
		return self::$pdo;
	}
}

?>