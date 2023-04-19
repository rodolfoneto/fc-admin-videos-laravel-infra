<?php

namespace Core\Domain\Validation;

use Core\Domain\Entity\Entity;
use Core\Domain\Entity\Genre;
use Rakit\Validation\Validator;

class GenreRakitValidator implements ValidatorInterface
{
    public function validate(Entity $entity): void
    {
        $entityData = $this->convertEntityToArray($entity);
        $validation = (new Validator)->validate($entityData, [
            'name' => 'required|min:2|max:255',
        ]);
        if($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $entity->notification->addError([
                    'context' => 'genre',
                    'message' => $error
                ]);
            }
        }
    }

    private function convertEntityToArray(Genre $entity): array
    {
        return [
            'name' => $entity->name,
        ];
    }
}
