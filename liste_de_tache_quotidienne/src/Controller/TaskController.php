<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categories;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;


class TaskController extends AbstractController
{
    /**
     * @Route("/task", name="task")
     */
    public function index()
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
      /**
     * @Route("/task/list", name="task_list")
     */
    
    public function listtask()
    {
        return $this->render('task/list.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
      /**
     * @Route("/task/ajouter", name="task_ajouter")
     */
      public function ajoutertask(Request $request,TranslatorInterface $translator)

    {

         $task = new Task();
        // $task->setTask('Write a blog post');
        // $task->setDueDate(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($task)
            ->add('nameTask', TextType::class,['attr'=> ['placeholder' => 'nom de la tache','class'=>'form-group']])
            ->add('dueDateTask', DateType::class,['widget' => 'single_text'])
             ->add('descriptionTask', TextareaType::class)
             ->add('PriorityTask', ChoiceType::class, [
                    'choices'  => [
                    'Hight' => 'Hight',
                    'medium' => 'Medium',
                    'low' => 'Low',
                ],
            ])
             ->add('category', EntityType::class, [
                    'class' => Categories::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.libelleCategory', 'ASC');
                    },
                    'choice_label' => 'libelleCategory',
                ])
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();
            $form->handleRequest($request);

            if($form->isSubmitted()&& $form->isValid()){
                $task=$form->getData();
                $task->setCreatedDateTask(new \DateTime('now'));

                $task->setCategoryTask("");

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();

                $this->addFlash('notice',$translator->trans('task.added'));


                return $this->redirectToRoute('task_liste');
            }



        return $this->render('task/ajouter.html.twig', [
            'controller_name' => 'TaskController','form'=>$form->createView()
        ]);
    }
      /**
     * @Route("/task/modifier/{id}", name="task_modifier")
     */
    public function modifiertask($id,Request $request,TranslatorInterface $translator)
    {
           $task = $this->getDoctrine()
           ->getRepository(task::class)
           ->findOneBy(array('idTask'=> $id));
        // $task->setTask('Write a blog post');
        // $task->setDueDate(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($task)
            ->add('nameTask', TextType::class,['attr'=> ['placeholder' => 'nom de la tache','class'=>'form-group']])
            ->add('dueDateTask', DateType::class,['widget' => 'single_text'])
             ->add('descriptionTask', TextareaType::class)
             ->add('PriorityTask', ChoiceType::class, [
                    'choices'  => [
                    'Hight' => 'Hight',
                    'medium' => 'Medium',
                    'low' => 'Low',
                ],
            ])
             ->add('category', EntityType::class, [
                    'class' => Categories::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.libelleCategory', 'ASC');
                    },
                    'choice_label' => 'libelleCategory',
                ])
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();
            $form->handleRequest($request);

            if($form->isSubmitted()&& $form->isValid()){
                $task=$form->getData();
                $task->setCreatedDateTask(new \DateTime('now'));

                $task->setCategoryTask("");

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();
$this->addFlash('notice',$translator->trans('task.updated'));
                return $this->redirectToRoute('task_liste');
            }



        return $this->render('task/modifier.html.twig', [
            'controller_name' => 'TaskController',
            'form'=>$form->createView()
        ]);
    }
      /**
     * @Route("/task/supprimer/{id}", name="task_supprimer")
     */
     public function supprimertask($id,TranslatorInterface $translator)
    {
         $task = $this->getDoctrine()
           ->getRepository(task::class)
           ->findOneBy(array('idTask'=> $id));
$entityManager=$this->getDoctrine()->getManager();
$entityManager->remove ($task);
$entityManager->flush();
$this->addFlash('notice',$translator->trans('task.deleted'));



        return $this->redirectToRoute('task_liste');
    }
     /**
     * @Route("/task/liste", name="task_liste")
     */
     public function taskliste()
    {
    	$repository= $this->getDoctrine()->getRepository
    	(Task::class);
    	$tasks=$repository->findall();
    	//dump($tasks);
        return $this->render('task/list.html.twig', [
            'controller_name' => 'TaskController',
            'tasks' => $tasks
        ]);
    }

}
