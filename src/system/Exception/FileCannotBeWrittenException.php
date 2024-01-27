<?php

namespace Exception;

use Exception\BaseException;

class FileCannotBeWrittenException extends BaseException {
  public function __construct($file, $line, $filepath, $message = null) {
    $msg = $message ?? "File '{$filepath}' cannot be written.";
    parent::__construct($file, $line, $msg);
  }
}
