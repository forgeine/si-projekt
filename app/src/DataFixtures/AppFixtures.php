<?php
/**
 * App Fixtures.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixtures.
 */
class AppFixtures extends Fixture
{
    /**
     * Loader.
     *
     * @param ObjectManager $manager Object Manager
     *
     * @return void Void
     */
    public function load(ObjectManager $manager): void
    {
        $manager->flush();
    }
}
