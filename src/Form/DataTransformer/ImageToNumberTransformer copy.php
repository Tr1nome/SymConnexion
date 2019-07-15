<?php

namespace App\Form\DataTransformer;

use App\Entity\Image;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;

class ImageToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (formation) to a string (number).
     *
     * @param  Image|null $formation
     * @return string
     */
    public function transform($formation)
    {
        if (null === $image) {
            return '';
        }

        return $image->getId();
    }

    /**
     * Transforms a string (number) to an object (formation).
     *
     * @param  string $imageNumber
     * @return Image|null
     * @throws TransformationFailedException if object (image) is not found.
     */
    public function reverseTransform($imageNumber)
    {
        // no issue number? It's optional, so that's ok
        if (!$imageNumber) {
            return;
        }

        $image = $this->entityManager
            ->getRepository(Image::class)
            // query for the issue with this id
            ->find($imageNumber)
        ;

        if (null === $image) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An image with number "%s" does not exist!',
                $imageNumber
            ));
        }

        return $image;
    }
}