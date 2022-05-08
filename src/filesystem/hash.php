<?php

/**
 * FILESYSTEM UTILITIES
 * 
 * @name        connect
 * @package     tools 
 * @version     1.01.001
 * @since       07-May-2022 10:41:23
 * @author      jonthompson
 * @abstract    
 */

namespace jthompson\tools\filesystem;
use \Exception;

class hash {
    const ERR_EMPTY         = 'Missing or empty filename';
    const ERR_INVALID_CHARS = 'Filename contains invalid characters (alphanumeric, dot, underscore and dash only)';
    
    /**
     * Return a path for a given filename to enable a more even filesystem distribution
     * 
     * Example: jthompson\tools\filesystem::hashPath('test.png', 2) -> '364/be8/'
     * 
     * @version 1.01.001
     * @date 07 May 2022
     * @author Jon Thompson <jon@jonthompson.co.uk>
     * 
     * @param string $filename The filename to create a hashPath for. Accepted characters are alphanumeric, dot, underscore and dash
     * @param int $levels The number of folder levels deep to create (1-5, default is 1)
     * @return string The hashPath
     * @throws Exception
     */
    static function hashPath(string $filename, int $levels = 1): string {
        if(empty(trim($filename))) {
            throw new Exception(self::ERR_EMPTY);
        } elseif(basename($filename) != $filename) {
            throw new Exception("Folder structure detected. Filename only required");
        } elseif(preg_match("/[^a-zA-Z0-9._-]/", $filename)) {
            throw new Exception(self::ERR_INVALID_CHARS);
        } elseif($levels > 5) {
            throw new Exception("Too many path levels");
        } else {
            $md5    = md5($filename);
            $parts  = str_split($md5, 3);
            $path   = "";
            
            for($i = 0; $i < $levels; $i++) {
                $path .= "{$parts[$i]}/";
            }
            
            return (string) $path;
        }
    }
}