<?php

/**
 * DATE FORMATTING TOOLS
 * 
 * @name        format
 * @package     library 
 * @version     1.01.001
 * @since       01-Aug-2019 13:17:08
 * @author      jonthompson
 * @abstract    
 */

namespace jthompson\tools\date;

class format {
    
    /**
     * Convert a MySQL-formatted date to "fancy" format
     * @version 1.01.001
     * @date 05 Mar 2020
     * @author Jon Thompson <jon@jonthompson.co.uk>
     * @param string $dateTime The MySQL-format dateTime to convert
     * @return string
     */
    public static function mysqlToFancyDateTime(string $dateTime): string {
        return (string) date("D jS F Y g:i:s A", strtotime($dateTime));
    }
    
    
    
    /**
     * Convert a MySQL-formatted date to "fancy" format
     * @version 1.01.001
     * @date 05 Mar 2020
     * @author Jon Thompson <jon@jonthompson.co.uk>
     * @param string $dateTime The MySQL-format dateTime to convert
     * @return string
     */
    public static function mysqlToFancyDate(string $dateTime): string {
        return (string) date("D jS F Y", strtotime($dateTime));
    }
    
    
    
    /**
     * Convert a dateTime string to MySQL format
     * @version 1.01.001
     * @date 05 Mar 2020
     * @author Jon Thompson <jon@jonthompson.co.uk>
     * @param string $dateTime The dateTime to convert
     * @return string
     */
    public static function timeStringToMysql(string $dateTime): string {
        return (string) date("Y-m-d H:i:s", strtotime($dateTime));
    }
}