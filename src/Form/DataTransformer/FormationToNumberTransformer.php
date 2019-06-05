<?php

namespace App\Form\DataTransformer;

use App\Entity\Formation;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;

class FormationToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (formation) to a string (number).
     *
     * @param  Formation|null $formation
     * @return string
     */
    public function transform($formation)
    {
        if (null === $formation) {
            return '';
        }

        return $formation->getId();
    }

    /**
     * Transforms a string (number) to an object (formation).
     *
     * @param  string $formationNumber
     * @return Formation|null
     * @throws TransformationFailedException if object (formation) is not found.
     */
    public function reverseTransform($formationNumber)
    {
        // no issue number? It's optional, so that's ok
        if (!$formationNumber) {
            return;
        }

        $formation = $this->entityManager
            ->getRepository(Formation::class)
            // query for the issue with this id
            ->find($formationNumber)
        ;

        if (null === $formation) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An taskList with number "%s" does not exist!',
                $formationNumber
            ));
        }

        return $formation;
    }
}