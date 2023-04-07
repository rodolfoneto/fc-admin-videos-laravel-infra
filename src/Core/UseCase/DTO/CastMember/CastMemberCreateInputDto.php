<?php

namespace Core\UseCase\DTO\CastMember;

class CastMemberCreateInputDto
{
    public function __construct(
        public string $name,
        public int $type,
    ) {

    }
}
