<?php

/**
 * @file
 * Contains \Drupal\fhir_restapi\EventSubscriber\fhirServerRedirectSubscriber
 */

namespace Drupal\fhir_restapi\EventSubscriber;

use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class fhirServerRedirectSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return([
      KernelEvents::REQUEST => [
        ['redirectMyContentTypeFhirServer'],
      ]
    ]);
  }

  /**
   * Redirect requests for fhir_servers node detail pages to 'fhir-datasets'.
   *
   * @param GetResponseEvent $event
   * @return void
   */
  public function redirectMyContentTypeFhirServer(GetResponseEvent $event) {
    $request = $event->getRequest();

    // This is necessary because this also gets called on
    // node sub-tabs such as "edit", "revisions", etc.  This
    // prevents those pages from redirected.
    if ($request->attributes->get('_route') !== 'entity.node.canonical') {
      return;
    }

    // Only redirect a fhir_servers content type.
    if ($request->attributes->get('node')->getType() !== 'fhir_servers') {
      return;
    }

    $baseUrl = $event->getRequest()->getBaseUrl();
    $attr = $event->getRequest()->attributes;
    if(null !== $attr &&
      null !== $attr->get('node') &&
      $attr->get('node')->get('type')->getString() == 'fhir_servers') {
      $event->setResponse(new RedirectResponse($baseUrl.'/fhir-datasets'));
    }

  }

}
