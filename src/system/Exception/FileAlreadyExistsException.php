<?php

namespace Exception;

use Exception\BaseException;

class FileAlreadyExistsException extends BaseException {
  public function __construct($file, $line, $filepath, $message = null) {
    $msg = $message ?? "File '{$filepath}' already exists.";
    parent::__construct($file, $line, $msg);
  }
}