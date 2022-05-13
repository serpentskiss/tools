<?php

/**
 * TITLE
 * 
 * @name        resize
 * @package     github.localdev 
 * @version     
 * @since       27-Mar-2020 10:56:13
 * @author      jonthompson
 * @abstract    
 */


namespace jthompson\tools\images;
use jthompson\tools\files;

class resize {
    public $src;
    public $dest;
    
    public function __construct() {
        $this->files = new files;
    }
    
    public function resize($source, int $width, int $height) {
        $dest = imagecreatetruecolor($width, $height);
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));
        return $dest;
    }
    
    public function centerCrop($source, int $width, int $height) {
        $source_w   = imagesx($source);
        $source_h   = imagesy($source);
        $offset_x   = ceil(($source_w - $width) / 2);
        $offset_y   = ceil(($source_h - $height) / 2);
        $dest       = imagecrop($source, ['x' => $offset_x, 'y' => floor($offset_y / 2), 'width' => $width, 'height' => $height]);
        return $dest;
    }
    
    
    public function createFromFile(string $filename) {
        $fileExtension = $this->files->getFileExtension($filename);
        
        switch($fileExtension) {
            case "jpg":
            case "jpeg":
                $src = imagecreatefromjpeg($filename);
                break;
            case "png":
                $src = imagecreatefrompng($filename);
                break;
            case "gif":
                $src = imagecreatefromgif($filename);
                break;
            default:
                throw new \Exception("Unsupported file type");
        }
        
        return $src;
    }
}