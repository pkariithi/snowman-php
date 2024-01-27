<?php

namespace Exception;

use Exception;

class BaseException extends Exception {
  public string $file;
  public int $line;
  public $code;
  public $message;

  public function __construct($file, $line, $message = "", $code = 0, Exception $prev = null) {
    $this->file = $file;
    $this->line = $line;
    $this->message = $message;
    $this->code = $code;

    parent::__construct($this->message, $this->code, $prev);
  }

  public function __toString(): string {
    return "[{$this->file}:{$this->line}] {$this->message}\n";
  }
}