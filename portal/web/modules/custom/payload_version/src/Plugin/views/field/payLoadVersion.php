<?php

namespace Drupal\payload_version\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Random;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("payload_versions")
 */
class payLoadVersion extends FieldPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function usesGroupBy()
    {
        return FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        // Do nothing -- to override the parent query.
    }

    /**
     * {@inheritdoc}
     */
    protected function defineOptions()
    {
        $options = parent::defineOptions();

        $options['hide_alter_empty'] = ['default' => FALSE];
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state)
    {
        parent::buildOptionsForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function render(ResultRow $values)
    {
        // Return a random text, here you can include your custom logic.
        // Include any namespace required to call the method required to generate
        // the desired output.
        $node = $values->_entity;
        $node = Node::load($node->id());
        if ($node) {
            // Get the value of payload collection
            $paragraph = $node->field_payload_collection->getValue();
            $name = [];
            foreach ($paragraph as $element) {
                // load the paragraph object.
                $paragraphObject = \Drupal\paragraphs\Entity\Paragraph::load($element['target_id']);
                if ($paragraphObject) {
                    $tid = $paragraphObject->field_payload_version->getValue()[0]['target_id'];
                    $term = Term::load($tid);
                    if ($term) {
                        $name[] = $term->getName();
                    }
                }
            }
            $payloadVersions = implode(", ", $name);
            return $payloadVersions;
        }
    }
}
