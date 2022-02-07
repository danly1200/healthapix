<?php

namespace Drupal\dexp_builder\Ajax;

use Drupal\Core\Ajax\CloseDialogCommand;

/**
 * Defines an AJAX command that closes the currently visible modal dialog.
 */
class CloseBuilderModalDialogCommand extends CloseDialogCommand {

  /**
   * Constructs a CloseModalDialogCommand object.
   *
   * @param bool $persist
   *   (optional) Whether to persist the dialog in the DOM or not.
   */
  public function __construct($selector = NULL, $persist = FALSE) {
    $this->selector = $selector == NULL? '#dexp-builder-modal' : $selector;
    $this->persist = $persist;
  }

}