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

namespace App\Controller;

use App\Entity\UserCustomer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ServerStatus\Application\Service\Check\ViewCheckByCustomerRequest;
use ServerStatus\Application\Service\Check\ViewChecksByCustomerRequest;
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/check")
 * @method UserCustomer getUser()
 */
class CheckController extends Controller
{
    /**
     * @Route("/", name="check_list")
     * @Security("has_role('ROLE_CUSTOMER')")
     */
    public function indexAction(Request $request)
    {
        $date = $request->get('date', 'now');
        $req = new ViewChecksByCustomerRequest(
            $this->getUser()->getCustomer()->id(),
            new \DateTimeImmutable($date),
            $request->get('type', DateRangeLast24Hours::NAME)
        );
        try {
            $reports = $this
                ->get('ServerStatus\Application\Service\Check\ViewPerformanceReportsService')
                ->execute($req);
            //dump($reports);

            return $this->render('check/index.html.twig', [
                'reports' => $reports
            ]);
        } catch (CustomerDoesNotExistException $e) {
            throw $this->createNotFoundException('not found', $e);
        }
    }

    /**
     * @Route("/{slug}", name="check_show")
     * @Security("has_role('ROLE_CUSTOMER')")
     */
    public function showAction(string $slug)
    {
        $req = new ViewCheckByCustomerRequest(
            $this->getUser()->getCustomer()->id(),
            new CheckName($slug, $slug)
        );
        $summary = $this->get('ServerStatus\Application\Service\Check\ViewCheckByCustomerService')
            ->execute($req);
        //dump($check);

        return $this->render('check/show.html.twig', [
            'summary' => $summary
        ]);
    }
}
