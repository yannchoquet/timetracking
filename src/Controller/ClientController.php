<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/admin/client")
 */
class ClientController extends Controller
{

    CONST ENTITY_NAME = 'client';
    /**
     * @Route("/", name="client_index", methods="GET")
     * @param ClientRepository $clientRepository
     * @return Response
     */
    public function index(ClientRepository $clientRepository): Response
    {

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $list = $serializer->normalize($clientRepository->findAll(), null, array(
            'attributes' => $this->getAttributesList()
        ));

        return $this->render('crud/index.html.twig', [
            'attributes' => $this->getAttributesList(),
            'list' => $list,
            'entity_name' => self::ENTITY_NAME,
        ]);
    }

    /**
     * @Route("/new", name="client_new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('client_index');
        }

        return $this->render('crud/new.html.twig', [
            'client' => $client,
            'entity_name' => self::ENTITY_NAME,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_show", methods="GET")
     * @param Client $client
     * @return Response
     */
    public function show(Client $client): Response
    {
        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($client, null, array(
            'attributes' => array(
                'id',
                'name'
            )
        ));

        return $this->render('crud/show.html.twig', [
            'data' => $data,
            'entity_name' => self::ENTITY_NAME
        ]);
    }

    /**
     * @Route("/{id}/edit", name="client_edit", methods="GET|POST")
     */
    public function edit(Request $request, Client $client): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_edit', ['id' => $client->getId()]);
        }

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($client, null, array(
            'attributes' => $this->getAttributesList()
        ));

        return $this->render('crud/edit.html.twig', [
            'data' => $data,
            'entity_name' => self::ENTITY_NAME,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_delete", methods="DELETE")
     * @param Request $request
     * @param Client $client
     * @return Response
     */
    public function delete(Request $request, Client $client): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($client);
            $em->flush();
        }

        return $this->redirectToRoute('client_index');
    }

    /**
     * @return array
     */
    public function getAttributesList(): array
    {
        return [
            'id',
            'name'
        ];
    }
}
