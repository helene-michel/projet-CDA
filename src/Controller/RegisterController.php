<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegisterController extends AbstractController{

    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route(path: '/creation_compte', name:'app_register')]
    public function Register(
            Request $request, EntityManagerInterface $em,
            UserPasswordHasherInterface $uphi,
            UserAuthenticatorInterface $userAuthenticator,
            #[Autowire(service: 'security.authenticator.form_login.main')] AuthenticatorInterface $authenticator
        ){
        $user = new User();
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data =$request->request->all();
            if($user->getPassword()!==$data['user']['confirm_password']){
                $this->addFlash('infos', 'le mot de passe et la confirmation ne correspondent pas.');
                return $this->redirectToRoute('app_register');
            }
            $passwordHash = $uphi->hashPassword($user, $user->getPassword());

            $user->setPassword($passwordHash);
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                new TemplatedEmail()
                    ->from(new Address('no-reply@${PROJECT_NAME}.fr', '${PROJECT_NAME}'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('register/register_email.html.twig')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

            return $this->redirectToRoute('app_main');
        }
        return $this->render('register/register.html.twig', [
            'form' => $form
        ]);
        
    }

     #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason(), [], 'VerifyEmailBundle');

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre addresse a bien été vérifé avec succés.');

        return $this->redirectToRoute('app_login');
    }
}