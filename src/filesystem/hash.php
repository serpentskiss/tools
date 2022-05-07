<?php

namespace jthompson\tools\filesystem;
use \Exception;

class hash {
    
    /**
     * Create a path for a given filename to enable a more even file distribution
     * 
     * @param string $filename The filename to create a hashPath for
     * @param int $levels The number of folder levels deep to create
     * @return string The hashPath
     * @throws Exception
     */
    static function hashPath(string $filename, int $levels = 1): string {
        $filename = trim($filename);
        
        if(empty($filename)) {
            throw new Exception("Missing filename");
        } elseif(preg_match("/[^a-zA-Z0-9._-]/", $filename)) {
            throw new Exception("Filename ($filename) contains invalid characters");
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