<?php

namespace Core\Domain\Validation;

use Core\Domain\Entity\Category;
use Core\Domain\Entity\Entity;
use Rakit\Validation\Validator;

class CategoryRakitValidator implements ValidatorInterface
{

    public function validate(Entity $entity): void
    {
        $entityData = $this->convertEntityToArray($entity);
        $validation = (new Validator)->validate($entityData, [
            'name' => 'required|min:3|max:255',
            'description' => 'nullable|max:255',
        ]);
        if($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $entity->notification->addError([
                    'context' => 'category',
                    'message' => $error
                ]);
            }
        }
    }

    private function convertEntityToArray(Category $entity): array
    {
        return [
            'name' => $entity->name,
            'description' => $entity->description,
        ];
    }
}
