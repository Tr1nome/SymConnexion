<?php

namespace App\Form\DataTransformer;

use App\Entity\Image;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskListToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (taskList) to a string (number).
     *
     * @param  Image|null $taskList
     * @return string
     */
    public function transform($taskList)
    {
        if (null === $taskList) {
            return '';
        }

        return $taskList->getId();
    }

    /**
     * Transforms a string (number) to an object (taskList).
     *
     * @param  string $taskListNumber
     * @return Tasklist|null
     * @throws TransformationFailedException if object (taskList) is not found.
     */
    public function reverseTransform($taskListNumber)
    {
        // no issue number? It's optional, so that's ok
        if (!$taskListNumber) {
            return;
        }

        $taskList = $this->entityManager
            ->getRepository(Image::class)
            // query for the issue with this id
            ->find($taskListNumber)
        ;

        if (null === $taskList) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An taskList with number "%s" does not exist!',
                $taskListNumber
            ));
        }

        return $taskList;
    }
}