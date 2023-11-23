<?php

namespace App\Controller;



use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\NewCardType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class NewCardController extends AbstractController
{
    #[Route('/card/new')]
    public function form(Request $request): Response
    {
        $form = $this->createForm(NewCardType::class);
        // dump($form);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newCard = $form->getData();
            $serializedCard = serialize($newCard);
            
            $fs = new Filesystem();

            try {
                $fs->appendToFile("uploads/cards.inc", $serializedCard . "\n");
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at " . $exception->getPath();
            };

            return $this->redirect('/card');
        }
        return $this->render('card/new.html.twig', [
            'form' => $form,
        ]);
    }
}