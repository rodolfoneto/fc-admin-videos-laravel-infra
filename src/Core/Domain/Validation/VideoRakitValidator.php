<?php

namespace Core\Domain\Validation;

use Rakit\Validation\Validator;
use Core\Domain\Entity\Entity;

class VideoRakitValidator implements ValidatorInterface
{

    public function validate(Entity $entity): void
    {
        $entityData = $this->convertEntityToArray($entity);
        $validation = (new Validator)->validate($entityData, [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'yearLaunched' => 'required|integer',
            'duration' => 'required|integer',
        ]);
        if($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $entity->notification->addError([
                    'context' => 'video',
                    'message' => $error
                ]);
            }
        }
    }

    private function convertEntityToArray(Entity $entity): array
    {
        return [
            'title' => $entity->title,
            'description' => $entity->description,
            'yearLaunched' => $entity->yearLaunched,
            'duration' => $entity->duration,
        ];
    }
}
