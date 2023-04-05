<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\{
    GenreUpdateInputDto,
    GenreUpdateOutputDto,
};
use Core\UseCase\Interface\TransactionInterface;
use Throwable;

class UpdateGenreUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository,
        protected TransactionInterface $transaction,
        protected CategoryRepositoryInterface $categoryRepository,
    ) {

    }

    public function execute(GenreUpdateInputDto $input): GenreUpdateOutputDto
    {
        $genre = $this->repository->findById($input->id);
        $genre->update($input->name);
        foreach ($input->categoriesId as $categoryId){
            $genre->addCategory($categoryId);
        }
        try {
            $this->validateCategoriesId($input->categoriesId);
            $this->repository->update($genre);
            $this->transaction->commit();

            return new GenreUpdateOutputDto(
                id: $genre->id(),
                name: $genre->name,
                is_active: $genre->isActive,
                // categories_id: $genre->categoriesId,
                created_at: $genre->createdAt(),
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
