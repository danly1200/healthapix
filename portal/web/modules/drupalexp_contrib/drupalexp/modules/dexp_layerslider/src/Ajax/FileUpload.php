<?php

namespace Drupal\dexp_layerslider\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class FileUpload implements CommandInterface {

  protected $fid;
  protected $url;
  protected $type;

  // Constructs a ReadMessageCommand object.
  public function __construct($fid, $url) {
    $this->fid = $fid;
    $this->url = $url;
  }

  // Implements Drupal\Core\Ajax\CommandInterface:render().
  public function render() {
    return array(
      'command' => 'UpdateImage',
      'url' => $this->url,
      'fid' => $this->fid,
    );
  }

}