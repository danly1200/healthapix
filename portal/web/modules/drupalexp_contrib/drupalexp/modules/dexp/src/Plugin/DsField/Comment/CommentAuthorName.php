<?php
namespace Drupal\dexp\Plugin\DsField\Comment;
use Drupal\ds\Plugin\DsField\Field;

/**
 * Plugin that renders the author of a comment.
 *
 * @DsField(
 *   id = "dexp_comment_author",
 *   title = @Translation("Author Name"),
 *   entity_type = "comment",
 *   provider = "comment"
 * )
 */
class CommentAuthorName extends Field{
    public function build() {
        $entity = $this->entity();
        return ['#markup' => $this->entity()->getOwner()->getDisplayName()];
    }
}