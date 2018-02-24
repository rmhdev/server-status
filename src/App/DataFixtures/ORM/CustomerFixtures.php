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
use Doctrine\Common\Persistence\ObjectManager;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Infrastructure\Domain\Model\Customer\DoctrineCustomerFactory;
use ServerStatus\Domain\Fixtures\Customer\FixturesCustomerData;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /* @var CustomerRepository $repository */
        $repository = $manager->getRepository(Customer::class);
        $data = new FixturesCustomerData($repository, new DoctrineCustomerFactory());
        $data->load();
    }
}
