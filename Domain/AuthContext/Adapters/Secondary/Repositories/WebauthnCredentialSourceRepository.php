<?php

namespace Domain\AuthContext\Adapters\Secondary\Repositories;


use Doctrine\Persistence\ManagerRegistry;
use Webauthn\{
    Bundle\Repository\DoctrineCredentialSourceRepository,
    PublicKeyCredentialSource
};
use Infrastructure\Entities\WebAuthnCredentialSource;

final class WebauthnCredentialSourceRepository extends DoctrineCredentialSourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebAuthnCredentialSource::class);
    }

    public function saveCredentialSource(PublicKeyCredentialSource $publicKeyCredentialSource): void
    {
        if (!$publicKeyCredentialSource instanceof WebAuthnCredentialSource) {
            $publicKeyCredentialSource = new WebAuthnCredentialSource(
                $publicKeyCredentialSource->publicKeyCredentialId,
                $publicKeyCredentialSource->type,
                $publicKeyCredentialSource->transports,
                $publicKeyCredentialSource->attestationType,
                $publicKeyCredentialSource->trustPath,
                $publicKeyCredentialSource->aaguid,
                $publicKeyCredentialSource->credentialPublicKey,
                $publicKeyCredentialSource->userHandle,
                $publicKeyCredentialSource->counter
            );
        }
        parent::saveCredentialSource($publicKeyCredentialSource);
    }
}
