<?php

namespace Helper;

use DirectoryIterator;
use Exception\FileAlreadyExistsException;
use Exception\FileDoesNotExistException;
use Exception\FileCannotBeOpenedException;
use Exception\FileCannotBeWrittenException;

class File {

  public static function exists($filepath) {
    return (file_exists($filepath) && is_file($filepath));
  }

  public static function readable($filepath) {
    return (file_exists($filepath) && is_file($filepath) && is_readable($filepath));
  }

  public static function delete($filepath) {
    return @unlink($filepath);
  }

  public static function rename($filepath, $newname) {
    if(File::exists($filepath)) {
      return rename($filepath, $newname);
    }
    throw new FileDoesNotExistException(__FILE__, __LINE__, $filepath);
  }

  public static function copy($filepath, $dest, $overwrite = false) {
    if(File::exists($filepath)) {
      if($overwrite) {
        return copy($filepath, $dest);
      } else {
        if(!File::exists($dest)) {
          return copy($filepath, $dest);
        } else {
          throw new FileAlreadyExistsException(__FILE__, __LINE__, $dest, "Cannot copy '{$filepath}' to '{$dest}'. '{$dest}' already exists and overwrite is false.");
        }
      }
    }
    throw new FileDoesNotExistException(__FILE__, __LINE__, $filepath, "Cannot copy '{$filepath}' to '{$dest}'. '{$filepath}' does not exist.");
  }

  public static function move($filepath, $dest) {
    $copy = File::copy($filepath, $dest);
    return $copy ? File::delete($filepath) : $copy;
  }

  public static function size($filepath) {
    if(File::exists($filepath)) {
      return filesize($filepath);
    }
    throw new FileDoesNotExistException(__FILE__, __LINE__, $filepath, "Cannot read '{$filepath}', it does not exist.");
  }

  public static function mime($filepath) {
    if(File::exists($filepath)) {
      return mime_content_type($filepath);
    }
    throw new FileDoesNotExistException(__FILE__, __LINE__, $filepath, "Cannot read '{$filepath}', it does not exist.");
  }

  public static function basename($filepath) {
    if(File::exists($filepath)) {
      return basename($filepath);
    }
    throw new FileDoesNotExistException(__FILE__, __LINE__, $filepath, "File '{$filepath}', it does not exist.");
  }

  public static function lastChange($filepath) {
    if(File::exists($filepath)) {
      return filemtime($filepath);
    }
    throw new FileDoesNotExistException(__FILE__, __LINE__, $filepath, "Cannot read '{$filepath}', it does not exist.");
  }

  public static function lastAccess($filepath) {
    if(File::exists($filepath)) {
      return fileatime($filepath);
    }
    throw new FileDoesNotExistException(__FILE__, __LINE__, $filepath, "Cannot read '{$filepath}', it does not exist.");
  }

  public static function readFileContents($filepath) {
    if(File::exists($filepath)) {
      return file_get_contents($filepath);
    }
    throw new FileDoesNotExistException(__FILE__, __LINE__, $filepath, "Cannot read '{$filepath}', it does not exist.");
  }

  public static function writeFileContents($content, $filepath, $create = true, $append = false, $chmod = 0644) {

    if(!$create && !File::exists($filepath)) {
      throw new FileDoesNotExistException(__FILE__, __LINE__, $filepath, "File '{$filepath}' cannot be written because it does not exist and cannot be created.");
    }

    File::dirCreate(dirname($filepath));

    $handler = $append ? @fopen($filepath, 'a') : @fopen($filepath, 'w');
    if($handler === false) {
      throw new FileCannotBeOpenedException(__FILE__, __LINE__,$filepath);
    }

    $error_reporting_level = error_reporting();
    error_reporting(0);

    $write = fwrite($handler, $content);
    if($write === false) {
      throw new FileCannotBeWrittenException(__FILE__,__LINE__,$filepath);
    }

    fclose($handler);
    chmod($filepath, $chmod);

    error_reporting($error_reporting_level);
    return true;
  }

  public static function dirExists($dirpath) {
    return (file_exists($dirpath) && is_dir($dirpath));
  }

  public static function dirCreate($dirpath, $chmod = 0755) {
    if(!File::dirExists($dirpath)) {
      mkdir($dirpath, $chmod, true);
    }
    return chmod($dirpath, $chmod);
  }

  public static function getFileList($dirpath, $path = false) {
    $files = [];
    $iterator = new DirectoryIterator($dirpath);
    foreach($iterator as $file) {
      if(!$file->isDot() && $file->isFile()) {
        $files[] = $path ? $file->getPathname() : $file->getFilename();
      }
    }
    return $files;
  }

  public static function getDirList($dirpath, $path = false) {
    $files = [];
    $iterator = new DirectoryIterator($dirpath);
    foreach($iterator as $file) {
      if(!$file->isDot() && $file->isDir()) {
        $files[] = $path ? $file->getPathname() : $file->getFilename();
      }
    }
    return $files;
  }

  public static function downlaod($filepath, $displayname = null) {
    if(!File::exists($filepath)) {
      throw new FileDoesNotExistException(__FILE__, __LINE__, $filepath, "Cannot download '{$filepath}', it does not exist.");
    }

    if(is_null($displayname)) {
      $displayname = File::basename($filepath);
    }

    if(!headers_sent()) {

      if(ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'off');
      }

      // headers
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: 0');
      header('Pragma: Public');
      header('Content-Description: File Transfer');
      header('Content-Type: '.File::mime($filepath));
      header('Content-Disposition: attachment;filename="'.$displayname.'"');
      header('Content-Length: '.File::size($filepath));

      // clear system output buffer
      ob_clean();
      flush();

      // force download
      readfile($filepath);
      return;
    }
  }

}
