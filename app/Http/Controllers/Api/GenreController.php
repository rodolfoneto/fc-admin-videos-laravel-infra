<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Http\Resources\GenreResource;
use Illuminate\Http\Request;
use Core\UseCase\DTO\Genre\{GenreCreateInputDto,
    GenreDeleteInputDto,
    GenreInputDto,
    GenresListInputDto,
    GenreUpdateInputDto};
use Core\UseCase\Genre\{CreateGenreUseCase,
    DeleteGenreUseCase,
    ListGenresUseCase,
    ListGenreUseCase,
    UpdateGenreUseCase};
use Illuminate\Http\Response;

class GenreController extends Controller
{

    public function index(Request $request, ListGenresUseCase $useCase)
    {
        $input = new GenresListInputDto(
            filter: $request->get('filter', ''),
            order: $request->get('order', 'DESC'),
            page: $request->get('page', 1),
            totalPerPage: $request->get('total_per_page', 15),
        );
        $response = $useCase->execute($input);
        return GenreResource::collection(collect($response->items))->additional([
            'meta' => [
                'total' => $response->total,
                'last_page' => $response->last_page,
                'first_page' => $response->first_page,
                'current_page' => $response->current_page,
                'per_page' => $response->per_page,
                'to' => $response->to,
                'from' => $response->from,
            ]
        ]);
    }

    public function store(StoreGenreRequest $request, CreateGenreUseCase $useCase)
    {
        $input = new GenreCreateInputDto(
            name: $request->name,
            categoriesId: $request->categories_id ?? [],
            is_active: $request->is_active ?? true,
        );
        $response = $useCase->execute($input);

        return (new GenreResource($response))
        ->response()
        ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ListGenreUseCase $useCase, $id)
    {
        $input = new GenreInputDto(id: $id);
        $response = $useCase->execute($input);
        return new GenreResource($response);
    }

    public function update(UpdateGenreRequest $request, $id, UpdateGenreUseCase $useCase)
    {
        $input = new GenreUpdateInputDto(
            id: $id,
            name: $request->name,
            categoriesId: $request->categories_id ?? [],
            is_active: $request->is_active ?? true,
        );
        $response = $useCase->execute($input);
        return new GenreResource($response);
    }

    public function destroy($id, DeleteGenreUseCase $useCase)
    {
        $input = new GenreDeleteInputDto(id: $id);
        $useCase->execute($input);
        return response()->noContent();
    }
}
