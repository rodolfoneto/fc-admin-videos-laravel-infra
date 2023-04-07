<?php

namespace Core\UseCase\DTO\CastMember;

class CastMembersListInputDto
{
    public function __construct(
        public string $filter = '',
        public string $order = 'DESC',
        public int $page = 1,
        public int $totalPerPage = 15,
    ) {

    }
}
