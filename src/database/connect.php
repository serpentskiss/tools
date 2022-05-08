<?php

/**
 * DATABASE CONNECTION WRAPPERS
 * 
 * @name        connect
 * @package     sharedlibraries.localdev 
 * @version     1.01.001
 * @since       04-Mar-2020 10:41:23
 * @author      jonthompson
 * @abstract    
 */



namespace jthompson\tools\database;
use \PDO;

class connect {
    /**
     * Connect to a database using PDO
     * @version 1.01.001
     * @date 04 Mar 2020
     * @author Jon Thompson <jon@jonthompson.co.uk>
     * @param string $hostname The MySQL server to connect to
     * @param string $username The MySQL username to log in with
     * @param string $password The password for the MySQL username
     * @param string $database (Optional, default = NULL) The database to connect to
     * @param string $characterSet (Optional, default = utf8mb4) The character set to use in the connection
     * @return PDO
     * @throws \Exception
     */
    public static function connectPDO(string $hostname, string $username, string $password, string $database = NULL, string $characterSet = "utf8mb4") {
        $conn = $database !== NULL  ? "mysql:dbname={$database};host={$hostname}"
                                    : "mysql:host={$hostname}";
        
        try {
            $db = new \PDO($conn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$characterSet}"));
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }

        return $db;
    }
}