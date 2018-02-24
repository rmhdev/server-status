<?php
declare(strict_types=1);

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ServerStatus\Domain\Fixtures\Check\FixturesCheckData;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Infrastructure\Domain\Model\Check\DoctrineCheckFactory;

class CheckFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /* @var CustomerRepository $customerRepo */
        $customerRepo = $manager->getRepository(Customer::class);
        /* @var CheckRepository $customerRepo */
        $checkRepo = $manager->getRepository(Check::class);
        $data = new FixturesCheckData($checkRepo, new DoctrineCheckFactory(), $customerRepo);
        $data->load();
    }

    public function getDependencies()
    {
        return [
            CustomerFixtures::class
        ];
    }


}
