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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ServerStatus\Domain\Model\Customer\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return new Response("ok", Response::HTTP_OK);
    }

    /**
     * @Route("/test", name="test")
     */
    public function testAction()
    {
        return $this->render("test.html.twig", [
            "customers" => $this->getDoctrine()->getRepository(Customer::class)
                ->createQueryBuilder("a")
                ->where("a.alias.value = :name")
                ->setParameter("name", 'Roberto')
                ->getQuery()->execute()

        ]);
    }
}
