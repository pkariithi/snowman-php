<?php

namespace Exception;

use Exception\BaseException;

class FileDoesNotExistException extends BaseException {
  public function __construct($file, $line, $filepath, $message = null) {
    $msg = $message ?? "File '{$filepath}' does not exist.";
    parent::__construct($file, $line, $msg);
  }
}