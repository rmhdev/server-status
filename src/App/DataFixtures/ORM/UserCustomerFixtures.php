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

use App\Entity\UserCustomer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use ServerStatus\Domain\Model\Customer\Customer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCustomerFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;


    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUsers() as $item) {
            $user = new UserCustomer();
            $password = $this->encoder->encodePassword($user, $item["password"]);
            $user
                ->setCustomer($this->findCustomer($item["username"]))
                ->setPassword($password)
                ->setUsername($item["username"])
            ;
            $manager->persist($user);
        }
        $manager->flush();
    }

    /**
     * @param string $username
     * @return Customer
     */
    private function findCustomer($username)
    {
        return $this->getReference("customer-" . $username);
    }

    private function getUsers()
    {
        yield([
            "username" => "rober@example.com",
            "password" => "123456",
        ]);
    }

    public function getDependencies()
    {
        return [
            CustomerFixtures::class
        ];
    }
}
