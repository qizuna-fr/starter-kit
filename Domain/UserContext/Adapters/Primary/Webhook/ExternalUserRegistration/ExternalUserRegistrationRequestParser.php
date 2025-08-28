<?php

declare(strict_types=1);

/** Qizuna 2025 - tous droits reservés  **/

namespace Domain\UserContext\Adapters\Primary\Webhook\ExternalUserRegistration;


use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

class ExternalUserRegistrationRequestParser extends AbstractRequestParser{

    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            // Add RequestMatchers to fit your needs
            //new SchemeRequestMatcher('https'),
            new MethodRequestMatcher('POST'),
            new IsJsonRequestMatcher()
        ]);
    }

    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): RemoteEvent|array|null
    {
        // TODO: Adapt or replace the content of this method to fit your need.

        // Validate the request against $secret.
        $authToken = $request->headers->get('X-Authentication-Token');
        //dd($authToken);

        if ($authToken !== $secret) {
            throw new RejectWebhookException(Response::HTTP_UNAUTHORIZED, 'Invalid authentication token.');
        }



        // Validate the request payload.
        //if (!$request->getPayload()->has('topic') || !$request->getPayload()->has('data')) {
        //    throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, 'Request payload does not contain required fields.');
        //}

        // Parse the request payload and return a RemoteEvent object.
        $payload = $request->getPayload();

        return new RemoteEvent(
            $payload->getString('topic'),
            $payload->getString('id'),
            $payload->all(),
        );
    }
}
