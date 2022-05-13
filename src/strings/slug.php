<?php

/**
 * WRAPPER FOR BEHAT TRANSLITERATOR
 * 
 * @name        slugs
 * @package     library 
 * @version     
 * @since       18-Jul-2019 15:00:34
 * @author      jonthompson
 * @abstract    
 */


namespace jthompson\tools\strings;
use Behat\Transliterator\Transliterator;
setlocale(LC_ALL, 'en_GB.UTF-8');

class slug {
    /**
     * Normalise a string and return it in all upper-case
     * @param string $text The text to normalise
     * @return string
     */
    public static function normaliseUpper(string $text): string {
        return (string) strtoupper(trim(self::toText($text)));
    }
    
    /**
     * Attempt to normalise a string into a standard format
     * EG "maria mckee" becomes "Maria McKee"
     * @param string $text The text to normalise
     * @return string
     */
    public static function normalise(string $text): string {
        $text = trim(self::toText($text));
        $text = ucwords(strtolower($text));
        
        if(preg_match("/ Mc([a-z])/", $text)) {
            $initial    = strtoupper(preg_replace("/^.+ Mc([a-z]).+$/", "\\1", $text));
            $text       = preg_replace("/ Mc([a-z])/", " Mc{$initial}", $text);
        }
        
        if(preg_match("/(^| )[^aeiouy]{2,}( |$)/i", $text)) {
            $out = [];
            preg_match_all("/(^| )([^aeiouy]{2,})( |$)/i", $text, $out);
            foreach($out[2] as $tmp) {
                $text = str_replace($tmp, strtoupper($tmp), $text);
            }
        }

        
        return (string) $text;
    }
    
    /**
     * Convert a string to a URL-safe
     * @param string $text
     * @return string
     */
    public static function toUrl(string $text, string $delimiter = "+"): string {
        return (string) Transliterator::transliterate($text, $delimiter);
    }
    
    public static function toText(string $text): string {
        return (string) Transliterator::transliterate($text, ' ');
    }
    
    public static function toFileName(string $text): string {
        return (string) Transliterator::transliterate($text, '_');
    }
    
    public static function toSort(string $text, bool $skipStartingThe = TRUE): string {
        $regexReplace = $skipStartingThe === TRUE ? "\\2" : "\\2, \\1";
        return (string) preg_replace("/^(the) (.+)$/i", "$regexReplace", Transliterator::transliterate($text, ' '));
    }
    
    public static function hash(string $text): string {
        return (string) hash('tiger192,3', $text, FALSE);
    }
    
    public static function hashDirectory(string $text, int $levels = 3): string {
        $filesafe   = self::toFileName($text);
        $hash       = self::hash($filesafe);
        $return     = "";
        
        for($ct = 0; $ct < $levels; $ct++) {
            $return .= "/" . substr(strrev($hash), $ct, 1);
        }
        
        $return .= "/" . $filesafe;
        
        return (string) $return;
    }
}