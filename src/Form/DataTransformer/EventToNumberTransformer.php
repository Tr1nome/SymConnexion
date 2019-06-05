<?php

namespace App\Form\DataTransformer;

use App\Entity\Event;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;

class EventToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (event) to a string (number).
     *
     * @param  Event|null $event
     * @return string
     */
    public function transform($event)
    {
        if (null === $event) {
            return '';
        }

        return $event->getId();
    }

    /**
     * Transforms a string (number) to an object (event).
     *
     * @param  string $eventNumber
     * @return Event|null
     * @throws TransformationFailedException if object (event) is not found.
     */
    public function reverseTransform($eventNumber)
    {
        // no issue number? It's optional, so that's ok
        if (!$eventNumber) {
            return;
        }

        $event = $this->entityManager
            ->getRepository(Event::class)
            // query for the issue with this id
            ->find($eventNumber)
        ;

        if (null === $event) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An taskList with number "%s" does not exist!',
                $eventNumber
            ));
        }

        return $event;
    }
}