<?php

namespace AppBundle\Controller\Crm;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Ticket;
use AppBundle\Entity\User;
use AppBundle\Entity\Bid;

/**
 * @Security("is_granted('ROLE_CRM_USER') or is_granted('ROLE_SUPERADMIN')")
 */
class CrmController extends Controller
{
    /**
     * @Route("/crm", name="crm_index")
     */
    public function indexAction()
    {
        $importantObjects = $this->getDoctrine()->getRepository('AppBundle:Object')->getImportant();
        return $this->render('crm/index.html.twig',[
            'importantObjects' => $importantObjects,
        ]);
    }

}