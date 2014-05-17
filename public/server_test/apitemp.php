<?php
    /*
        Este archivo lee el archivo de CSV en donde estan los datos
        de prueba a mostrar, lo que hace es mostrar la primra linea
        del archivo la guarda en una variable, esta la compara para 
        guardar el nuevo archivo sin la linea mostrada.

    */

     $datos = file("datos.csv");
     $salida = array();
     echo $a_borrar= trim($datos[0]);
 
     foreach($datos as $linea) {
     	
         if(trim($linea) != $a_borrar) {
             $salida[] = $linea;
         }
       
     }

     $fp = fopen("datos.csv", "w+");
     flock($fp, LOCK_EX);
     foreach($salida as $linea) {
         fwrite($fp, $linea);
     }
     flock($fp, LOCK_UN);
     fclose($fp);  
?> 
