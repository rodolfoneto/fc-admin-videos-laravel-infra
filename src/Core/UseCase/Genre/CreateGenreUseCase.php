<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\{
    GenreCreateInputDto,
    GenreOutputDto,
};
use Core\UseCase\Interface\TransactionInterface;
use Throwable;

class CreateGenreUseCase
{

    protected GenreRepositoryInterface $repository;
    protected TransactionInterface $transaction;
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        GenreRepositoryInterface $repository,
        TransactionInterface $transaction,
        CategoryRepositoryInterface $categoryRepository,
    ) {
        $this->repository = $repository;
        $this->transaction = $transaction;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute(GenreCreateInputDto $input): GenreOutputDto
    {
        try {
            $genre = new Genre(
                name: $input->name,
                id: null,
                isActive: $input->is_active,
                categoriesId: $input->categoriesId,
            );
            $this->validateCategoriesId($input->categoriesId);
            $entity = $this->repository->insert($genre);
            $this->transaction->commit();

            return new GenreOutputDto(
                id: $entity->id(),
                name: $entity->name,
                is_active: $entity->isActive,
                created_at: $entity->createdAt(),
            );
        } catch (Throwable $th) {
            $this->transaction->rollback();
            throw $th;
        }
    }

    public function validateCategoriesId(array $categoriesId = [])
    {
        $categoriesIdDb = $this->categoryRepository->getIdsListIds($categoriesId);

        $idsDiff = array_diff($categoriesId, $categoriesIdDb);
        if (count($idsDiff) > 0) {
            $msg = sprintf(
                '%s %s not found',
                count($idsDiff) > 1 ? 'Categories' : 'Category',
                implode(', ', $idsDiff)
            );
            throw new NotFoundException($msg);
        }
    }
}
