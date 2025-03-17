<?php

namespace Infrastructure\Entities;


use Doctrine\ORM\Mapping as ORM;
use Domain\AuthContext\Adapters\Secondary\Repositories\WebauthnCredentialSourceRepository;
use Symfony\Component\Uid\Uuid;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\TrustPath\TrustPath;

#[ORM\Table(name: 'webauthn_credentials')]
#[ORM\Entity(repositoryClass: WebauthnCredentialSourceRepository::class)]
class WebAuthnCredentialSource extends PublicKeyCredentialSource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        string $publicKeyCredentialId,
        string $type,
        array $transports,
        string $attestationType,
        TrustPath $trustPath,
        Uuid $aaguid,
        string $credentialPublicKey,
        string $userHandle,
        int $counter
    ) {
        parent::__construct(
            $publicKeyCredentialId, $type, $transports,
            $attestationType,$trustPath,
            $aaguid, $credentialPublicKey,
            $userHandle, $counter
        );
    }
}
