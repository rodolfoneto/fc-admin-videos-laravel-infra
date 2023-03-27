<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{
    StoreCategoryRequest,
    UpdateCategoryRequest
};
use App\Http\Resources\CategoryResource;
use Core\UseCase\Category\{
    CreateCategoryUseCase,
    DeleteCategoryUseCase,
    ListCategoriesUseCase,
    ListCategoryUseCase,
    UpdateCategoryUseCase,
};
use Core\UseCase\DTO\Category\{
    CategoriesListInputDto,
    CategoryCreateInputDto,
    CategoryDeleteInputDto,
    CategoryInputDto,
    CategoryUpdateInputDto
};
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{

    public function index(Request $request, ListCategoriesUseCase $useCase)
    {
        $inputDto = new CategoriesListInputDto(
            filter: $request->get('filter', ''),
            order: $request->get('order', 'DESC'),
            page: (int) $request->get('page', 1),
            totalPerPage: (int) $request->get('total_per_page', 15),
        );
        $response = $useCase->execute($inputDto);
        return CategoryResource::collection(collect($response))
                                ->additional([
                                    'meta' => [
                                        'total' => $response->total,
                                        'last_page' => $response->last_page,
                                        'first_page' => $response->first_page,
                                        'per_page' => $response->per_page,
                                        'to' => $response->to,
                                        'from' => $response->from,
                                    ]
                                ]);
    }

    public function store(StoreCategoryRequest $request, CreateCategoryUseCase $useCase)
    {
        $inputDto = new CategoryCreateInputDto(
            name: $request->name,
            description: $request->description ?? '',
            is_active: (bool) $request->is_active ?? true,
        );

        $response = $useCase->execute(input: $inputDto);

        return (new CategoryResource(collect($response)))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($id, ListCategoryUseCase $useCase)
    {
        $input = new CategoryInputDto($id);
        $result = $useCase->execute($input);
        return (new CategoryResource(collect($result)))->response();
    }

    public function update(UpdateCategoryRequest $request, $id, UpdateCategoryUseCase $useCase)
    {
        $inputDto = new CategoryUpdateInputDto(
            id: $id,
            name: $request->name,
        );
        $outputDto = $useCase->execute($inputDto);
        return (new CategoryResource(collect($outputDto)))->response();
    }

    public function delete($id, DeleteCategoryUseCase $useCase)
    {
        $input = new CategoryDeleteInputDto(
            id: $id,
        );
        $useCase->execute($input);
        return response()->noContent();
    }
}
