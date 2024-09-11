<?php

function pdoConnection() {
    try {



        $db_name     = 'ace_db_1';

        $db_user     = 'root';

        $db_password = '';

        $db_host     = 'localhost';



        $connect = new PDO('mysql:host=' . $db_host . '; dbname=' . $db_name.';charset=utf8', $db_user, $db_password);

        $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



        return $connect;



       } catch (PDOException $e) {

           echo $e->getMessage();

       }

}
?>
