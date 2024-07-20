<?php

declare(strict_types=1);

namespace Infrastructure\DataFixtures;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Infrastructure\Entities\Tenant as TenantEntity;

final class Tenant extends Fixture
{
    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->generateDefault());

        $manager->flush();
    }

    private function generateDefault(): TenantEntity
    {
        $tenant = new TenantEntity();
        $tenant->setUuid('tenant-id-1');
        $tenant->setName('Tenant 1');
        $tenant->setCreatedAt(new DateTimeImmutable());
        $tenant->setCreatedBy('admin');
        $tenant->setAddress('Address');
        $tenant->setCity('City');
        $tenant->setZipCode('12345');
        $tenant->setIsMaster(true);

        $this->setReference('tenant-1', $tenant);

        return $tenant;
    }


}
