<?php
require_once('includesWeb/config.php');
require_once('includesWeb/provincia.php');

/*Clase encargada de actualizar la información del objeto Usuario en la BBDD*/
class DAOConsultorProvincia {

        public function getAllProvincias(){
            $db = getConexionBD();
            $sql ="SELECT CODIGO, NOMBRE FROM provincias";
            $result = mysqli_query($db, $sql);
            if(!$result){
                return false;
            }
            $elements = array();
            while($resultado = mysqli_fetch_assoc($result)){
                $provincia = new Provincia();
                $provincia->setCodigo($resultado['CODIGO']);
                $provincia->setNombre($resultado['NOMBRE']);
                array_push($elements, $provincia);
            }

            return $elements;
        }
    
}
?>