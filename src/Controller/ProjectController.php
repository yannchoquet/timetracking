<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/admin/project")
 */
class ProjectController extends Controller
{
    CONST ENTITY_NAME = 'project';

    /**
     * @Route("/", name="project_index", methods="GET")
     * @param ProjectRepository $projectRepository
     * @return Response
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        $serializer = new Serializer(array(new ObjectNormalizer()));
        $list = $serializer->normalize($projectRepository->findAll(), null, array(
            'attributes' => $this->getAttributesList()
        ));

        foreach($list as &$item){
            $item['client'] = $item['client']['name'];
        }

        $attributes = $this->getAttributesList();
        $attributes['client'] = 'client';

        return $this->render('crud/index.html.twig', [
            'attributes' => $attributes,
            'list' => $list,
            'entity_name' => self::ENTITY_NAME,
        ]);
    }

    /**
     * @Route("/new", name="project_new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('crud/new.html.twig', [
            'project' => $project,
            'entity_name' => self::ENTITY_NAME,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods="GET")
     * @param Project $project
     * @return Response
     */
    public function show(Project $project): Response
    {
        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($project, null, array(
            'attributes' => $this->getAttributesList()
        ));

        $data['client'] = $data['client']['name'];

        return $this->render('crud/show.html.twig', [
            'data' => $data,
            'entity_name' => self::ENTITY_NAME
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods="GET|POST")
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_edit', ['id' => $project->getId()]);
        }

        return $this->render('crud/edit.html.twig', [
            'data' => $project,
            'entity_name' => self::ENTITY_NAME,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods="DELETE")
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
        }

        return $this->redirectToRoute('project_index');
    }

    /**
     * @return array
     */
    public function getAttributesList(): array
    {
        return [
            'id',
            'name',
            'client' => ['name'],
            'timeGoal'
        ];
    }
}
