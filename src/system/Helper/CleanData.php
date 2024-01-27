<?php

namespace Helper;

class CleanData {

  public static function escape(string $value): string {
    return str_replace(
      ["\\", "\0", "\n", "\r", "\x1a", "'", '"'],
      ["\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'],
      $value
    );
  }

  public static function preventXSS($value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }

}
