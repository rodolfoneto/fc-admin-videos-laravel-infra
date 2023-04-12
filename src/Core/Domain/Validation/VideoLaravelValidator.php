<?php

namespace Core\Domain\Validation;

use Core\Domain\Entity\Entity;
use Illuminate\Support\Facades\Validator;

class VideoLaravelValidator implements ValidatorInterface
{

    public function validate(Entity $entity): void
    {
        $entityData = $this->convertEntityToArray($entity);
        $validator = Validator::make($entityData, [
            'title' => 'required|min:2|max:255',
            'description' => 'required|min:2|max:255',
            'yearLaunched' => 'required|integer',
            'duration' => 'required|integer',
        ]);

        if($validator->fails()) {
            foreach ($validator->errors()->messages() as $error) {
                $entity->notification->addError([
                    'context' => 'video',
                    'message' => $error[0],
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
