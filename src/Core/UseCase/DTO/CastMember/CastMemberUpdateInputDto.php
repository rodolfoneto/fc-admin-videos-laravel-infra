<?php

namespace Core\UseCase\DTO\CastMember;

class CastMemberUpdateInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public int $type,
    ) {

    }
}
