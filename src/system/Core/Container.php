<?php

namespace Core;

class Container {

  public static $values = [];

  public function set(string $key, $value = null): void {
    Container::$values[$key] = $value;
  }

  public function has(string $key): bool {
    return isset(Container::$values[$key]);
  }

  public function get(string $key) {
    if($this->has($key)) {
      return get_class($this)::$values[$key];
    }
    return null;
  }

}