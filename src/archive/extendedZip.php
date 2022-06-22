<?php

namespace jthompson\tools\archive;


/**
 * Wrapper for a more functional ZIP command.
 */
class extendedZip extends ZipArchive {
    // Member function to add a whole file system subtree to the archive
    public function addTree(string $sourceDirectory, string $localname = ''):void {
        if ($localname) {
            $this->addEmptyDir($localname);
        }

        $this->_addTree($sourceDirectory, $localname);
    }

    // Internal function, to recurse
    protected function _addTree(string $sourceDirectory, string $localname):void {
        if(basename($dir) !== 'cache') {
            $dir = opendir($sourceDirectory);

            while ($filename = readdir($dir)) {
                // Discard . and ..
                if(in_array($filename, array('.', '..', basename(__FILE__)))) {
                    continue;
                }

                // Proceed according to type
                $path = $dirname . '/' . $filename;
                $localpath = $localname ? ($localname . '/' . $filename) : $filename;

                if (is_dir($path)) {
                    // Directory: add & recurse
                    $this->addEmptyDir($localpath);
                    $this->_addTree($path, $localpath);
                } elseif (is_file($path)) {
                    // File: just add
                    $this->addFile($path, $localpath);
                }
            }

            closedir($dir);
        }
    }

    // Helper function
    public static function zipTree(string $sourceDirectory, string $zipFilename, int $flags = 0, string $localname = ''):void {
        $zip = new self();
        $zip->open($zipFilename, $flags);
        $zip->addTree($sourceDirectory, $localname);
        $zip->close();
    }
}
