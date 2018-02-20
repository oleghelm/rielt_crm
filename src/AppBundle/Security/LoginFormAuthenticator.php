<?php

namespace AppBundle\Security;

use AppBundle\Form\LoginForm;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $formFactory;
    private $em;
    private $router;
    private $passwordEncoder;
    
    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router, UserPasswordEncoder $passwordEncoder)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }
    
    protected function getLoginUrl(){
        return $this->router->generate('security_login');
    }
    
    protected function getDefaultSuccessRedirectUrl(){
        return $this->router->generate('homepage');
    }
    // check password here. if returns true? then user authorized, else user gets error
    public function checkCredentials($credentials, UserInterface $user): bool {
        $password = $credentials['_password'];
        
        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;//if true, then runs getDefaultSuccessRedirectUrl
        }
        //if fail, then runs getLoginUrl
        return false;
    }

    public function getCredentials(Request $request) {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            // skip authentication
            return;
        }
        //create form, using refactored here form class
        $form = $this->formFactory->create(LoginForm::class);
        $form->handleRequest($request);
        
        $data = $form->getData();
        
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['_username']
        );
        //if returns not null, then sybfony runs getUser
        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        $username = $credentials['_username'];
        //if returns null we get fail with error. if returns User object, symfony runs checkCredentials
        return $this->em->getRepository('AppBundle:User')->findOneBy(['email' => $username]);
    }

}