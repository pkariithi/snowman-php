<?php

namespace Service;

class Flash {

  private Session $session;
  private Response $response;
  private $flash = null;

  public function __construct(Session $session, Response $response) {
    $this->session = $session;
    $this->response = $response;
    $this->saveFlash($this->get());
  }

  public function set($message = [], $class = 'info', $redirect = false) {

    if(!is_array($message)) {
      $message = [$message];
    }

    $this->session->set('flash', [
      'message' => $message,
      'class' => $class
    ]);

    if($redirect) {
      $url = strtolower($redirect);
      $this->response->setStatusCode(302);
      $this->response->redirect($url);
    }
    return;
  }

  public function get() {
    if($this->session->has('flash')) {
      return $this->session->pull('flash');
    }
    return false;
  }

  public function saveFlash($flash = null) {
    if(!empty($flash)) {
      $this->flash = $flash;
    }
  }

  public function displayFlash() {
    return $this->flash;
  }

}

