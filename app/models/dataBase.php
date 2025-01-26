<?php
namespace app\models;

use PDO, PDOException, Exception;

if(file_exists(__DIR__."/../../config/server.php")){
    require_once __DIR__."/../../config/server.php";
}

class dataBase {
    // Parámetros de conexión a la base de datos
    private $servidor;
    private $userDB;
    private $userPass;
    private $nameDB;
    private $conectado;

    // Instancia única para el patrón Singleton
    private static $instance = null;

    // Constructor privado para evitar instanciación externa
    private function __construct() {
        // Asignar valores de configuración a las propiedades
        $this->servidor = DB_SERVER ; // Servidor de la base de datos
        $this->userDB = DB_USER;     // Usuario de la base de datos
        $this->userPass = DB_PASS;   // Contraseña del usuario
        $this->nameDB = DB_NAME;     // Nombre de la base de datos
        $this->conectar();                 // Iniciar la conexión
    }

    // Método estático para obtener la instancia de la conexión
    public static function startConnection() {
        // Verificar si ya existe una instancia
        if (self::$instance === null) {
            self::$instance = new self(); // Crear una nueva instancia si no existe
        }
        return self::$instance; // Retornar la instancia
    }

    // Método privado para establecer la conexión a la base de datos
    private function conectar() {
        try {
            // Crear una nueva conexión PDO
            $this->conectado = new PDO("mysql:host={$this->servidor};dbname={$this->nameDB}", $this->userDB, $this->userPass);
            // Configurar el modo de error de PDO
            $this->conectado->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Establecer el conjunto de caracteres a UTF-8
            $this->conectado->exec("SET CHARACTER SET utf8");

        } catch (PDOException $error) {
            // Registrar el error en el log
            error_log('Error de conexión: ' . $error->getMessage());
            // Lanzar una excepción con un mensaje genérico
            throw new Exception('Error de conexión a la base de datos. Por favor, inténtelo más tarde.');
        }
    }

    // Método para obtener la conexión establecida
    public function getConnection() {
        return $this->conectado; // Retornar la conexión
    }

    // Método para cerrar la conexión
    public function disconnect() {
        $this->conectado = null; // Cerrar la conexión
    }
}