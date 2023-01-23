<?php

namespace App\Controller;
use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// class IngredientController extends AbstractController
// {
//     #[Route('ingredient', name: 'app_ingredient')]
//     public function index(IngredientRepository $repository): Response //injection des dependences ingredientrepository
//     {
//         $ingredients = $repository->findAll();
      
//         return $this->render('pages/ingredient/index.html.twig', [
//             'ingredients' => $ingredients
//         ]);
//     }
// }


// ou


class IngredientController extends AbstractController
{
  /**
   * Cette fonction affiche tous les ingrédients
   *
   * @param IngredientRepository $repository
   * @param PaginatorInterface $paginator
   * @param Request $request
   * @return void
   */
    #[Route('ingredient', name: 'app_ingredient', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface  $paginator,  Request $request): Response //injection des dependences ingredientrepository
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }
    #[Route('/ingredient/nouveau', 'ingredient.new',  methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ) : Response
    {
        $ingredient =  new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
         $ingredient = $form->getData();

         $manager->persist($ingredient); //pour ajouter dans bdd
         $manager->flush();

         $this->addFlash(
             'Success',
             'Votre ingrédient a été créé avec succès ! '
         );
            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
