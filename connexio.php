<?php

    /*
    * @autor Alberto Pascual
    * 
    */
    class persistencia {
        private $gbd;
        private $stmt;
        
        function __construct() {
            $this->usuari = "a6587501_muss";
            $this->contrasenya = "qwerty12";
            try {
                $this->gbd = new PDO('mysql:host=mysql2.000webhost.com;dbname=a6587501_must', $this->usuari, $this->contrasenya);
            } catch(PDOException $Exception){
                echo "Error en la conexion";
            }
        } 
        public function getGbd(){
            return $this->gbd;
        }
    }
     
?>
