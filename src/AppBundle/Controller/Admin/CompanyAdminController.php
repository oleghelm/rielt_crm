<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Company;
use AppBundle\Repository\CompanyRepository;
use AppBundle\Form\CompanyFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Service\FileUploader;
/**
 * @Security("is_granted('ROLE_ADMIN_PANEL_USER') or is_granted('ROLE_SUPERADMIN')")
 * @Route("/admin")
 */
class CompanyAdminController extends Controller
{
    /**
     * @Route("/company", name="admin_company_list")
     */
    public function indexAction()
    {
        $items = $this->getDoctrine()
            ->getRepository('AppBundle:Company')
            ->findAll();

        return $this->render('admin/company/list.html.twig', array(
            'items' => $items
        ));
    }

    /**
     * @Route("/company/new", name="admin_company_new")
     */
    public function newAction(Request $request, FileUploader $fileUploader)
    {
        $form = $this->createForm(CompanyFormType::class);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();
            
            //work with file
            $file = $company->getLogo();
            if($file){
                $fileUploader->setCurrentDir($this->getParameter('company_photo_directory'));
                $fileName = $fileUploader->upload($file);
                $company->setLogo($fileName);
            }
            $file = $company->getPreview();
            if($file){
                $fileUploader->setCurrentDir($this->getParameter('company_photo_directory'));
                $fileName = $fileUploader->upload($file);
                $company->setPreview($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            $this->addFlash('success', 'Компанію створено');

            return $this->redirectToRoute('admin_company_list');
        }

        return $this->render('admin/company/new.html.twig', [
            'form' => $form->createView(),
            'item' => null
        ]);
    }
    
    /**
     * @Route("/company/{id}/edit", name="admin_company_edit")
     */
    public function editAction(Request $request, Company $company, FileUploader $fileUploader)
    {
        if(file_exists($this->getParameter('company_photo_directory').'/'.$company->getLogo()) && is_file($this->getParameter('company_photo_directory').'/'.$company->getLogo()))
            $company->setLogo($company->getLogo());
        $old_logo = $company->getLogo();
        
        if(file_exists($this->getParameter('company_photo_directory').'/'.$company->getPreview()) && is_file($this->getParameter('company_photo_directory').'/'.$company->getPreview()))
            $company->setPreview($company->getPreview());
        $old_preview = $company->getPreview();
//dump($this->getParameter('company_photo_directory'));
        $form = $this->createForm(CompanyFormType::class, $company);
        
        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();

            //work with file
            $file = $company->getLogo();
            if($file){
                if(file_exists($this->getParameter('company_photo_directory').'/'.$old_logo)){
                    unlink($this->getParameter('company_photo_directory').'/'.$old_logo);
                }
                $fileUploader->setCurrentDir($this->getParameter('company_photo_directory'));
                $fileName = $fileUploader->upload($file);
                $company->setLogo($fileName);
            } else {
                $company->setLogo($old_logo);
            }
            $file = $company->getPreview();
            if($file){
                if(file_exists($this->getParameter('company_photo_directory').'/'.$old_preview)){
                    unlink($this->getParameter('company_photo_directory').'/'.$old_preview);
                }
                $fileUploader->setCurrentDir($this->getParameter('company_photo_directory'));
                $fileName = $fileUploader->upload($file);
                $company->setPreview($fileName);
            } else {
                $company->setPreview($old_preview);
            }
            
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            $this->addFlash('success', 'Компанію оновлено!');

            return $this->redirectToRoute('admin_company_list');
        }

        return $this->render('admin/company/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $company,
        ]);
    }
    
        
    /**
     * @Route("/company/{id}/delete", name="admin_company_delete")
     */
    public function deleteAction(Request $request, Company $company){
        //check permissions if SuperUser Or Owner
        if (!$company) {
            throw $this->createNotFoundException('No user found');
        }
        
        $photo = $company->getLogo();
        if($photo){unlink($this->getParameter('company_photo_directory').'/'.$company->getPhoto());}
        $photo = $company->getPreview();
        if($photo){unlink($this->getParameter('company_photo_directory').'/'.$company->gettPreview());}

        $em = $this->getDoctrine()->getEntityManager();
        
//        $em->getRepository('AppBundle:Company')->unlinkUserFromEntitys($company);
        $em->remove($company);
        $em->flush();

        $this->addFlash('success', 'Компанію видалено');
        
        return $this->redirectToRoute('admin_company_list');
    }
}