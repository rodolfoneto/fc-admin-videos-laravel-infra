<?php

namespace Core\UseCase\DTO\CastMember;

class CastMemberDeleteOutputDto
{
    public function __construct(
        public bool $success,
    ) {
    }
}
