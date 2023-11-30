<?php

namespace App\Controller;

use App\Form\Type\EditCardType;
use App\Form\Type\NewCardType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class CardController extends AbstractController
{
    #[Route('/card', name: 'app_card')]
    public function importCard(): Response
    {
        $server = $this->getParameter('kernel.project_dir');

        $cardsArr = unserialize(file_get_contents($server . "/storage/cards.inc"));
        
        return $this->render('card/index.html.twig', [
            'preparedArr' => $cardsArr,
        ]);
    }

    #[Route('/card/edit/{id}', name: 'app_card_edit_id')]
    public function cardEdit(Request $request, int $id): Response
    {
        $server = $this->getParameter('kernel.project_dir');
        $cardsArr = unserialize(file_get_contents($server . "/storage/cards.inc"));
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(EditCardType::class, null, ['empty_data' => $cardsArr[$id]]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $editedCard = $form->getData();
            $cardsArr[(int) $id] = $editedCard;

            try {
                file_put_contents($server . "/storage/cards.inc", serialize($cardsArr));
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at " . $exception->getPath();
            };

            return $this->redirect('/card');
        }
        return $this->render('card/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/card/new')]
    public function cardCreate(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $server = $this->getParameter('kernel.project_dir');

        $cardsArr = unserialize(file_get_contents($server . "/storage/cards.inc"));
        // dd($cardsArr);
        $form = $this->createForm(NewCardType::class);
        // dump($form);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newCard = $form->getData();
            $cardsArr[] = $newCard;

            try {
                file_put_contents($server . "/storage/cards.inc", serialize($cardsArr));
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