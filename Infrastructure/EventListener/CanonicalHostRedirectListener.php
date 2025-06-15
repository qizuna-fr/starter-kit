<?php

declare(strict_types=1);

/** Qizuna 2025 - tous droits reservés  **/

namespace Infrastructure\EventListener;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CanonicalHostRedirectListener{

    private ?string $canonicalHost;

    public function __construct(?string $canonicalHost)
    {
        $this->canonicalHost = $canonicalHost;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Ne rien faire si pas de host défini → ex: en local
        if (!$this->canonicalHost || !$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentHost = $request->getHost();

        // Redirige seulement si le host actuel est différent de celui attendu
        if ($currentHost !== $this->canonicalHost) {
            $newUrl = $request->getScheme() . '://' . $this->canonicalHost . $request->getRequestUri();
            $event->setResponse(new RedirectResponse($newUrl, 301));
        }
    }

}
