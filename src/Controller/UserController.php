<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @Route("/")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $personneConnect = $this->getUser();
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'personneConnect'=> $personneConnect,
        ]);
    }

    /**
     * @Route("/user/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger, UserPasswordHasherInterface $hasher): Response
    {   
        $admin = $this->getUser();
        if($admin){
            if(in_array("ROLE_RH", $admin->getRoles())){
                $user = new User();
                $form = $this->createForm(UserType::class, $user);
                $form->handleRequest($request);
    
                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();
                    if($user->getSecteur()=='RH'){
                        $user->setRoles(['ROLE_RH']);
                    }
                    elseif($user->getSecteur()=='Informatique'){
                        $user->setRoles(['ROLE_INFORMATIQUE']);
                    }
                    elseif($user->getSecteur()=='Comptabilité'){
                        $user->setRoles(['ROLE_COMPTABILITE']);
                    }
                    elseif($user->getSecteur()=='Direction'){
                        $user->setRoles(['ROLE_DIRECTION']);
                    }
                    $photo = $form->get('photo')->getData();
                        if ($photo) {
                            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();
                            try {
                                $photo->move(
                                    $this->getParameter('brochures_directory'),
                                    $newFilename
                                );
                            } 
                            catch (FileException $e) {}
                            $user->setPhoto($newFilename);
                        }
                    $user->setEmail($user->getPrenom().$user->getNom()."@humanbooster.com");
                    $user->setPassword($hasher->hashPassword($user,$user->getPassword()));
                    $entityManager->persist($user);
                    $entityManager->flush();
    
                    return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
                }
            }
            else{
                return $this->redirectToRoute('user_index');
            }
        }
        
        else{
            return $this->redirectToRoute('user_index');
        }
        

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/user/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $personneConnect = $this->getUser();
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'personneConnect'=>$personneConnect,
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user,UserPasswordHasherInterface $hasher,SluggerInterface $slugger): Response
    {
        $admin = $this->getUser();
        if($admin){
            if(in_array("ROLE_RH", $admin->getRoles())){
                $form = $this->createForm(UserType::class, $user);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $this->getDoctrine()->getManager()->flush();
                    if($user->getSecteur()=='RH'){
                        $user->setRoles(['ROLE_RH']);
                    }
                    elseif($user->getSecteur()=='Informatique'){
                        $user->setRoles(['ROLE_INFORMATIQUE']);
                    }
                    elseif($user->getSecteur()=='Comptabilité'){
                        $user->setRoles(['ROLE_COMPTABILITE']);
                    }
                    elseif($user->getSecteur()=='Direction'){
                        $user->setRoles(['ROLE_DIRECTION']);
                    }
                    $photo = $form->get('photo')->getData();
                        if ($photo) {
                            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();
                            try {
                                $photo->move(
                                    $this->getParameter('brochures_directory'),
                                    $newFilename
                                );
                            } 
                            catch (FileException $e) {}
                            $user->setPhoto($newFilename);
                        }
                    $user->setEmail($user->getPrenom().$user->getNom()."@humanbooster.com");
                    $user->setPassword($hasher->hashPassword($user,$user->getPassword()));
                    return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
                }
            }
        }
        else{
            return $this->redirectToRoute('user_index');
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/user/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        $admin = $this->getUser();
        if($admin){
            if(in_array("ROLE_RH", $admin->getRoles())){
                if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($user);
                    $entityManager->flush();
                }

                return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        else{
            return $this->redirectToRoute('user_index');
        }
    }
}
