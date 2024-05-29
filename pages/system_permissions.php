<?php

    function checkPermissions($user_id, $permission_id) { 

        require_once('config.php');

        $connect = pdoConnection();     

        try {

            $sql  = 'SELECT

                     count(*) AS total_permissions

                     FROM system_permission_to_roles

                     LEFT JOIN system_users_to_roles

                     ON system_permission_to_roles.role_id = system_users_to_roles.role_id

                     WHERE system_users_to_roles.user_id = :user_id

                     AND system_permission_to_roles.permission_id = :permission_id

                    '; 

             $data = [

                     'user_id'       => $user_id,

                     'permission_id' => $permission_id

                     ];  

             $stmt = $connect->prepare($sql);

             $stmt->execute($data);

             $row  = $stmt->fetch();

             $authorized = ''; 

             if ($row['total_permissions'] > 0) {

                 $authorized = "true";

             } else {

                 $authorized = "false";

             }

             return $authorized;

        } catch (Exception $e) {

            echo $e->getMessage();

        }

}