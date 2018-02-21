<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Repository\ClientRepository;
use AppBundle\Form\UserFormType;
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
class UserAdminController extends Controller
{
    /**
     * @Route("/users", name="admin_user_list")
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();

        return $this->render('admin/user/list.html.twig', array(
            'users' => $users
        ));
    }

    /**
     * @Route("/users/new", name="admin_user_new")
     */
    public function newAction(Request $request, FileUploader $fileUploader)
    {
//        $user = new User;
//        $user->setPhoto(new File(null));
        $form = $this->createForm(UserFormType::class);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            
            //work with file
            $file = $user->getPhoto();
            if($file){
                $fileUploader->setCurrentDir($this->getParameter('user_photo_directory'));
                $fileName = $fileUploader->upload($file);
                $user->setPhoto($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User created!');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/new.html.twig', [
            'userForm' => $form->createView(),
            'user' => null
        ]);
    }
    
    /**
     * @Route("/users/{id}/edit", name="admin_user_edit")
     */
    public function editAction(Request $request, User $user, FileUploader $fileUploader)
    {
        if(file_exists($this->getParameter('user_photo_directory').'/'.$user->getPhoto()) && is_file($this->getParameter('user_photo_directory').'/'.$user->getPhoto()))
            $user->setPhoto($user->getPhoto());
//            $user->setPhoto(new File($this->getParameter('user_photo_directory').'/'.$user->getPhoto()));
        $old_photo = $user->getPhoto();
        $form = $this->createForm(UserFormType::class, $user);
        
        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            //work with file
            $file = $user->getPhoto();
            if($file){
                if(file_exists($this->getParameter('user_photo_directory').'/'.$old_photo)){
                    unlink($this->getParameter('user_photo_directory').'/'.$old_photo);
                }
                $fileUploader->setCurrentDir($this->getParameter('user_photo_directory'));
                $fileName = $fileUploader->upload($file);
                $user->setPhoto($fileName);
            } else {
                $user->setPhoto($old_photo);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User updated!');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/edit.html.twig', [
            'userForm' => $form->createView(),
            'user' => $user,
        ]);
    }
    
        
    /**
     * @Route("/clients/{id}/delete", name="admin_user_delete")
     */
    public function deleteAction(Request $request, User $user){
        //check permissions if SuperUser Or Owner
        if (!$user) {
            throw $this->createNotFoundException('No user found');
        }
        
//        $photo = $user->getPhoto();
//        if($photo){
//            unlink($this->getParameter('user_photo_directory').'/'.$user->getPhoto());
//        }

        $em = $this->getDoctrine()->getEntityManager();
        
        //unlink user from tables
        $em->getRepository('AppBundle:User')->unlinkUserFromEntitys($user);
        
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'User deleted!');
        
        return $this->redirectToRoute('admin_user_list');
    }
}