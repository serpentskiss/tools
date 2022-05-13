<?php

/**
 * DATETIME VALIDATION
 * 
 * @name        validate
 * @package     github.localdev 
 * @version     1.01.001
 * @since       13-Mar-2020 10:44:29
 * @author      jonthompson
 * @abstract    
 */

namespace jthompson\tools\date;

class validate {

    public static function validateMysql($date) {
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $d && $d->format('Y-m-d H:i:s') == $date;
    }

}
