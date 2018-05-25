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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/alert")
 * @method UserCustomer getUser()
 */
class AlertController extends Controller
{
    /**
     * @Route("/", name="alert_list")
     * @Security("has_role('ROLE_CUSTOMER')")
     */
    public function indexAction(Request $request)
    {
        return $this->render('alert/index.html.twig');
    }
}
