<?php

namespace App\Controller;
use App\Entity\Crud;
use App\Form\CrudType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class MainController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}
    
    
    #[Route('/main', name: 'main')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $data = $doctrine->getRepository(Crud::class)->findAll();
        dump ($data);
        return $this->render('main/index.html.twig', [
            'list' => $data,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function createProduct(ManagerRegistry $doctrine, Request $request): Response
    
    {
        $crud = new Crud();
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($crud);
            $em->flush();
            $this->addFlash('notice','Se ha creado la película correctamente!!');
            return $this->redirectToRoute('main');
        }
        return $this->render('main/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    
    #[Route('/update({id}', name: 'update')]
    public function update(ManagerRegistry $doctrine, Request $request, $id): Response
    {
       $crud = $doctrine->getRepository(Crud::class)->find($id);
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($crud);
            $em->flush();
            $this->addFlash('notice','Se ha actualizado correctamente!!');
            return $this->redirectToRoute('main');
        }
        return $this->render('main/update.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/delete({id}', name: 'delete')]
    public function delete(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $crud = $doctrine->getRepository(Crud::class)->find($id);
        $em = $doctrine->getManager();
        $em->remove($crud);
        $em->flush();
        $this->addFlash('notice','Se ha borrado correctamente!!');
        return $this->redirectToRoute('main');
    }


    #[Route('/genero/{category}', name: 'category')]
    
    public function getGenero(ManagerRegistry $doctrine,$category): Response
    {
        dump ($category);
        $data = $doctrine->getRepository(Crud::class)->findBy(['category' => $category]);
        dump ($data);
        return $this->render('main/genero.html.twig', [
            'list' => $data,
        ]);
    }

}
