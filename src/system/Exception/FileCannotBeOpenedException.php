<?php

namespace Exception;

use Exception\BaseException;

class FileCannotBeOpenedException extends BaseException {
  public function __construct($file, $line, $filepath, $message = null) {
    $msg = $message ?? "File '{$filepath}' cannot be opened.";
    parent::__construct($file, $line, $msg);
  }
}
