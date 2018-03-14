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
use ServerStatus\Domain\Fixtures\Measurement\FixturesMeasurementData;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Infrastructure\Domain\Model\Measurement\DoctrineMeasurementFactory;

class MeasurementFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /* @var MeasurementRepository $measurementRepo */
        $measurementRepo = $manager->getRepository(Measurement::class);
        /* @var CheckRepository $customerRepo */
        $checkRepo = $manager->getRepository(Check::class);

        $data = new FixturesMeasurementData($measurementRepo, new DoctrineMeasurementFactory(), $checkRepo);
        $data->load();
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CheckFixtures::class
        ];
    }
}
