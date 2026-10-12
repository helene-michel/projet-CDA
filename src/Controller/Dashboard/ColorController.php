<?php

namespace App\Controller\Dashboard;

use App\Entity\Color;
use App\Form\ColorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin/color')]
final class ColorController extends AbstractController
{   
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/{id}', name: 'app_color_admin', defaults: ['id' => null], requirements:['id' => '[0-9]+'])]
    public function index(Request $request, ?Color $color= null): Response
    {

        $colorList = $this->em->getRepository(Color::class)->findAll();
        $colorUpdate = true;
        $boutonText = "modifier";
        if(!$color){
            $color = new Color;
            $colorUpdate = false;
            $boutonText = "ajouter";
        }
        
        $form =  $this->createForm(ColorType::class, $color);
        $form->handleRequest($request);
        if($form->isSubmitted()  && $form->isValid()){
            if(!$colorUpdate){
                $this->em->persist($color);
            }
            
            $this->em->flush();
            return $this->redirectToRoute('app_color_admin');
        }

        return $this->render('admin/color/index.html.twig', [
            'colorList' => $colorList,
            'form' => $form,
            'boutonText' => $boutonText,
        ]);
    }
    
    #[Route('/delete', name:'app_color_delete_admin', methods:['DELETE'])]
    public function delete(Request $request): Response
    {  
        $id = $request->request->get('colorId');
        if($this->isCsrfTokenValid('color_delete_'.$id, $request->request->get('_token'))){
            $color = $this->em->getRepository(Color::class)->find($id);
            $this->em->remove($color);
            $this->em->flush();
            $this->addFlash('success', 'la suppression a été effectué avec succès.');
        }
        
        return $this->redirectToRoute('app_color_admin');
    }
}
