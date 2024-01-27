<?php

namespace Service;

class Session {
  public function start(string $name = ""): void {
    if(session_status() == PHP_SESSION_NONE) {
      if(!empty($name)) {
        session_name($name);
      }
      session_start();
    }
  }

  public function has($key): bool {
    return isset($_SESSION[$key]);
  }

  public function set(string $key, $value): void {
    $_SESSION[$key] = $value;
  }

  public function setMultiple(array $array): void {
    foreach($array as $key => $val) {
      $_SESSION[$key] = $val;
    }
  }

  public function get($key) {
    if($this->has($key)) {
      return $_SESSION[$key];
    }
    return false;
  }

  public function delete($key): void {
    unset($_SESSION[$key]);
  }

  public function pull($key) {
    if($this->has($key)) {
      $val = $this->get($key);
      $this->delete($key);
      return $val;
    }
    return false;
  }

  public function id(): string {
    return session_id();
  }

  public function regenerate($deleteOldSession = false): string {
    session_regenerate_id($deleteOldSession);
    return $this->id();
  }

  public function destroy() : void{
    if(session_status() != PHP_SESSION_NONE) {
      $this->regenerate(true);
      $_SESSION = [];
      session_destroy();
    }
  }

}
