<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Repository\MedicosRepository;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var MedicoFactory
     */
    private $medicoFactory;
    /**
     * @var MedicosRepository
     */
    private $medicosRepository;
    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory,
        MedicosRepository $medicosRepository,
        EspecialidadeRepository $especialidadeRepository,
    ) {
        $this->entityManager = $entityManager;
        $this->medicoFactory = $medicoFactory;
        $this->medicosRepository = $medicosRepository;
        $this->especialidadeRepository = $especialidadeRepository;
    }

    /**
     * @Route("/medicos")
     */
    public function indexMedicosAction()
    {
        $medicoList = $this->medicosRepository->findAll();
        return $this->render('medicos/show.html.twig',[
            'medicos' => $medicoList,
        ]);
    }

    /**
     * @Route("/medicos/create")
     */
    public function createMedicosAction()
    {
        $medicoList = $this->medicosRepository->findAll();
        $especialidadeList = $this->especialidadeRepository->findAll();

        return $this->render('medicos/create.html.twig',[
            'medicos' => $medicoList,
            'especialidades' => $especialidadeList,
        ]);
    }

    /**
     * @Route("/medicos/edit/{id}")
     */
    public function editMedicosAction(int $id)
    {
        $medicoList = $this->medicosRepository->find($id);
        $especialidadeList = $this->especialidadeRepository->findAll();

        return $this->render('medicos/edit.html.twig',[
            'medicos' => $medicoList,
            'especialidades' => $especialidadeList,
        ]);
    }

    /**
     * @Route("/api/medicos", methods={"POST"})
     */
    public function novo(Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $medico = $this->medicoFactory->criarMedico($corpoRequisicao);
        
        $this->entityManager->persist($medico);
        $this->entityManager->flush();

        return new JsonResponse($medico);
    }

    /**
     * @Route("/api/medicos", methods={"GET"})
     */
    public function buscarTodos(): Response
    {
        $medicoList = $this->medicosRepository->findAll();

        return new JsonResponse($medicoList);
    }

    /**
     * @Route("/api/medicos/{id}", methods={"GET"})
     */
    public function buscarUm(int $id): Response
    {
        $medico = $this->buscaMedico($id);
        $codigoRetorno = is_null($medico) ? Response::HTTP_NO_CONTENT : 200;

        return new JsonResponse($medico, $codigoRetorno);
    }

    /**
     * @Route("/api/medicos/{id}", methods={"PUT"})
     */
    public function atualiza(int $id, Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $medicoEnviado = $this->medicoFactory->criarMedico($corpoRequisicao);

        $medicoExistente = $this->buscaMedico($id);
        if (is_null($medicoExistente)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $medicoExistente
            ->setCrm($medicoEnviado->getCrm())
            ->setNome($medicoEnviado->getNome())
            ->setEspecialidade($medicoEnviado->getEspecialidade());
        $this->entityManager->flush();

        return new JsonResponse($medicoExistente);
    }

    /**
     * @Route("/api/medicos/{id}", methods={"DELETE"})
     */
    public function remove(int $id): Response
    {
        $medico = $this->buscaMedico($id);
        $this->entityManager->remove($medico);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param int $id
     * @return object|null
     */
    public function buscaMedico(int $id)
    {
        $medico = $this->medicosRepository->find($id);

        return $medico;
    }

    /**
     * @Route("/api/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */
    public function buscaPorEspecialidade(int $especialidadeId): Response
    {
        $medicos = $this->medicosRepository->findBy([
            'especialidade' => $especialidadeId
        ]);

        return new JsonResponse($medicos);
    }
}