<?php

/**
 * TITLE
 * 
 * @name        files
 * @package     library 
 * @version     
 * @since       10-Jul-2019 15:37:12
 * @author      jonthompson
 * @abstract    
 */

namespace jthompson\tools\filesystem;

class files {
    const ERR_MISSING_BINARY    = 'Binary exceutable not installed';
    const ERR_EMPTY_INPUT       = 'Blank/empty input passed to function';
    const ERR_NON_ZERO_RETURN   = 'A non-zero error code was returned';
    const ERR_DIR_MISSING       = 'Directory not found';
    const ERR_DIR_INVALID       = 'Invalid or empty directory path';
    const ERR_FILTER_EMPTY      = 'Empty file extension filter';
    const ERR_COPY_FAIL         = 'Cannot copy file';
    
    
    /**
     * Create a new folder
     * @param string $fullPathToFolder
     * @return bool
     * @throws \Exception
     */
    public static function createFolder(string $fullPathToFolder): bool {
        if(file_exists($fullPathToFolder) && is_dir($fullPathToFolder)) {
            return (bool) TRUE;
        } elseif(!file_exists($fullPathToFolder) && !is_dir($fullPathToFolder)) {
            if(!mkdir($fullPathToFolder, 0755, TRUE)) {
                throw new \Exception("Cannot create folder: {$fullPathToFolder}");
            } else {
                return (bool) TRUE;
            }
        }
    }
    
    
    
    /**
     * Return the contents of a given directory (non-recursive)
     * 
     * @param string $path The path to scan
     * @return array List of files and folders
     */
    public function getDirectoryList(string $path): array {
        $this->validateDirectory($path);
        $directoryIterator  = new \RecursiveDirectoryIterator($path);
        $files              = $this->scanDirectory($directoryIterator);
        return (array) $files;
    }
    
    
    
    /**
     * Return the contents of a given directory (non-recursive) that match the file extensions listed in the filter array
     * 
     * @param string $path The path to scan
     * @param array $filter List of file extensions to search for
     * @return array List of files found in the directory matching the filters
     */
    public function getDirectoryListWithFilter(string $path, array $filter): array {
        $files              = $this->getDirectoryList($path);
        $cleanedFilter      = $this->validateFilter($filter);
        $return             = [];
        
        foreach($files as $file) {
            $fileExtension = $this->getFileExtension($file);
            
            if(in_array($fileExtension, $cleanedFilter)) {
                $return[] = $file;
            }
        }
        
        return (array) $return;
    }
    
    
    
    /**
     * Return the contents of a given directory (recursive)
     * 
     * @param string $path The path to scan
     * @return array List of files and folders
     */
    public function getRecursiveDirectoryList(string $path): array {
        $this->validateDirectory($path);
        $directoryIterator  = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $files              = $this->scanDirectory($directoryIterator);
        return (array) $files;
    }
    
    
    /**
     * Recursively copy a folder
     * @param string $sourcePath
     * @param string $destinationPath
     * @return bool
     * @throws \Exception
     */
    public function copyDirectory(string $sourcePath, string $destinationPath): bool {
        $this->validateDirectory($sourcePath);
        self::createFolder($destinationPath);
        $parentDirectory = basename($sourcePath);
        $fileList = $this->getRecursiveDirectoryList($sourcePath);
        
        foreach($fileList as &$entry) {
            $entry = str_replace($sourcePath . '/', "", $entry);
            self::createFolder(dirname("{$destinationPath}/{$parentDirectory}/{$entry}"));
            
            try {
                if(!copy("{$sourcePath}/{$entry}", "{$destinationPath}/{$parentDirectory}/{$entry}")) {
                    throw new \Exception(ERR_COPY_FAIL . ": {$sourcePath}/{$entry} to {$destinationPath}/{$parentDirectory}/{$entry}");
                }
            } catch (\Exception $ex) {
                echo "{$ex->getMessage()}\n";
            }
        }
        
        return (bool) TRUE;
    }
    
    /**
     * Return the contents of a given directory (recursive) that match the file extensions listed in the filter array
     * 
     * @param string $path The path to scan
     * @param array $filter List of file extensions to search for
     * @return array List of files found in the directory matching the filters
     */
    public function getRecursiveDirectoryListWithFilter(string $path, array $filter): array {
        $files              = $this->getRecursiveDirectoryList($path);
        $cleanedFilter      = $this->validateFilter($filter);
        $return             = [];

        foreach($files as $file) {
            $fileExtension = $this->getFileExtension($file);
            
            if(in_array($fileExtension, $cleanedFilter)) {
                $return[] = $file;
            }
        }
        
        return (array) $return;
    }
    
    
    
    /**
     * Scan a directory
     * 
     * @param object $directoryIterator
     * @return array
     */
    private function scanDirectory(object $directoryIterator): array {
        $files              = [];
        $ignore             = ['.', '..'];
        
        foreach ($directoryIterator as $file) {
            if(!in_array($file->getFilename(), $ignore)) {
                $files[] = $file->getPathname();
            }
        }
        
        return (array) $files;
    }
    
    
    /**
     * Recursively delete a folder and all conetnts
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function deleteDirectoryRecursive(string $path): bool {
        $this->validateDirectory($path);
        
        $it     = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files  = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        
        foreach($files as $file) {
            if ($file->isDir()){
                if(!rmdir($file->getRealPath())) {
                    throw new \Exception("Error deleting {$file->getRealPath()}");
                }
            } else {
                if(!unlink($file->getRealPath())) {
                    throw new \Exception("Error deleting {$file->getRealPath()}");
                }
            }
        }
        
        if(!rmdir($path)) {
            throw new \Exception("Error deleting {$path}");
        }
        
        return (bool) TRUE;
    }
    
    /**
     * Return the size of a folder in bytes
     * @param string $path
     * @return int
     */
    public function getDirectorySize(string $path): int {
        $this->validateDirectory($path);
        
        $bytesTotal = 0;
        $path       = realpath($path);

        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
            $bytesTotal += $object->getSize();
        }
        
        return (int) $bytesTotal;
    }
    
    
    /**
     * Remove unsafe characters from file paths
     * 
     * @param string $text
     * @return string
     */
    public function sanitizePath(string $text): string {
        $reservedCharacters = str_split("\\/?%*:|\"<>. ");
        
        return (string) str_replace($reservedCharacters, "_", $text);
    }
    
    
    
    /**
     * Return the file extension of a file
     * 
     * @param string $pathToFile
     * @return string
     */
    public function getFileExtension(string $pathToFile): string {
        return (string) strtolower(pathinfo($pathToFile, PATHINFO_EXTENSION));
    }
    
    public function getFileName(string $pathToFile): string {
        return (string) strtolower(pathinfo($pathToFile, PATHINFO_FILENAME));
    }
    
    
    
    /**
     * Check if a given directory exists
     * 
     * @param string $path
     * @return bool
     */
    public function checkDirectoryExists(string $path): bool {
        if(!file_exists($path) && !is_dir($path)) {
            return (bool) FALSE;
        } else {
            return (bool) TRUE;
        }
    }
    
    
    
    /**
     * Check that a given path is a valid directory/folder
     * 
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function validateDirectory(string $path): bool {
        if($path == "") {
            throw new \Exception(self::ERR_DIR_INVALID);
        } elseif(!$this->checkDirectoryExists($path)) {
            throw new \Exception(self::ERR_DIR_MISSING . " : {$path}");
        } else {
            return (bool) TRUE;
        }
    }
    
    
    
    /**
     * Sanitize an array of file extensions
     * 
     * @param array $filter
     * @return array
     * @throws \Exception
     */
    public function validateFilter(array $filter): array {
        if(empty($filter)) {
            throw new \Exception(self::ERR_FILTER_EMPTY);
        } else {
            foreach($filter as &$filterEntry) {
                $filterEntry = strtolower(preg_replace("/^\./", "", $filterEntry));
            }
            
            return (array) $filter;
        }
    }
    
    
    
    /**
     * Get the full path to a program or binary file
     * 
     * @param string $binaryName
     * @return string
     * @throws \Exception
     */
    public function getFullPathToBinary(string $binaryName): string {
        if(trim($binaryName) == '') {
            throw new \Exception(self::ERR_EMPTY_INPUT);
        } else {
            $cmd    = "which --skip-alias " . escapeshellarg($binaryName);
            $out    = [];
            $return = NULL;
            
            exec($cmd, $out, $return);
            
            if($return != 0) {
                throw new \Exception(self::ERR_NON_ZERO_RETURN . " : Error code {$return} - {$cmd}");
            } elseif(empty($out)) {
                throw new \Exception(self::ERR_MISSING_BINARY . " : {$binaryName}");
            } else {
                return (string) trim($out[0]);
            }
        }
    }
    
    /**
     * Convert bytes to human-readable format
     * @param int $size
     * @return string
     */
    public function humanSize(int $size): string {
        switch($size) {
            case($size >= 1024 * 1024 * 1024): 
                $hSize = round($size / (1024 * 1024 * 1024), 2) . " Gb";
                break;
            case($size >= 1024 * 1024): 
                $hSize = round($size / (1024 * 1024), 2) . " Mb";
                break;
            case($size >= 1024): 
                $hSize = round($size / (1024), 2) . " kb";
                break;
            default:
                $hSize = "$size bytes";
                break;
        }

        return (string) $hSize;
    }
    

    /**
     * PHP emulation of the tail command. Return {$lines} lines from the end of a file
     * @param string $filename The filename to look in
     * @param int $lines Number of lines to return
     * @param int $buffer
     * @return array
     * @throws \Exception
     */
    public static function tail(string $filename, int $lines = 10, int $buffer = 4096): array {
        if(!$f = fopen($filename, "rb")) {
            throw new \Exception("Cannot open file {$filename}");
        }

        fseek($f, -1, SEEK_END);

        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }

        $output = '';
        $chunk  = '';

        while (ftell($f) > 0 && $lines >= 0) {
            $seek = min(ftell($f), $buffer);
            fseek($f, -$seek, SEEK_CUR);
            $output = ($chunk = fread($f, $seek)) . $output;
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            $lines -= substr_count($chunk, "\n");
        }

        while ($lines++ < 0) {
            $output = substr($output, strpos($output, "\n") + 1);
        }

        fclose($f);
        return (array) explode("\n", $output);
    }

}
