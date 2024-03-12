<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

uses()->group('unit')->in('Domain/AuthContext/Unit');

uses(KernelTestCase::class)->beforeEach(function () {
    $this->bootedKernel = $this::bootKernel(["environment" => "test"]);
    $container = $this->bootedKernel->getContainer();
    $this->entityManager = $container->get('doctrine')->getManager();
    $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    cleanDatabase($this->entityManager, $this->databaseTool);
})
    ->group('integration')
    ->in('Domain/AuthContext/Integration');


uses(WebTestCase::class)->beforeEach(function () {
    $this->client = $this::createClient(["environment", "test"]);
    $container = $this->client->getContainer();
    $this->entityManager = $container->get('doctrine')->getManager();
    $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    cleanDatabase($this->entityManager, $this->databaseTool);
})->group('e2e')->in('DDomain/AuthContext/E2E');


/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toBeDifferentFrom', function ($expected) {
    Assert::assertNotSame($expected, $this->value);
    return $this;
});

expect()->extend('toContainOneOfType', function (string $class) {
    $bool = false;

    foreach ($this->value as $value) {
        if ($value instanceof $class) {
            $bool = true;
        }
    }

    Assert::assertTrue($bool, "Failed asserting that array contains one element of type {$class}");
    return $this;
});

expect()->extend('toMatchUuid', function () {
    Assert::matchesRegularExpression('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $this->value);
    return $this;
});




/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function cleanDatabase(EntityManagerInterface $entityManager, AbstractDatabaseTool $databaseTool): void
{
    $databaseTool->loadAllFixtures();
}
