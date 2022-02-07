<?php

namespace Drupal\dexp_builder\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\Core\Ajax\BaseCommand;

class TextParse implements CommandInterface {

  protected $text;
  protected $text_format;

  // Constructs a ReadMessageCommand object.
  public function __construct($text, $text_format = 'drupalexp_builder') {
    $this->text = $text;
    $this->text_format = $text_format;
  }

  // Implements Drupal\Core\Ajax\CommandInterface:render().
  public function render() {
    return array(
      'command' => 'shortcodesParse',
      'elements' => ['title' => 'Title', 'map' => 'Map'],
      'text' => $this->text,
    );
  }

}
