<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCastMemberRequest;
use App\Http\Requests\UpdateCastMemberRequest;
use App\Http\Resources\CastMemberResource;
use Core\UseCase\CastMember\{
    CreateCastMemberUseCase,
    DeleteCastMemberUseCase,
    ListCastMembersUseCase,
    ListCastMemberUseCase,
    UpdateCastMemberUseCase,
};
use Core\UseCase\DTO\CastMember\{
    CastMemberDeleteInputDto,
    CastMemberListInputDto,
    CastMembersListInputDto,
    CastMemberUpdateInputDto,
    CastMemberCreateInputDto,
    CastMemberOutputDto,
};
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CastMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ListCastMembersUseCase $useCase)
    {

        $response =  $useCase->execute(new CastMembersListInputDto(
            filter: $request->get('filter', ''),
            order: $request->get('order', 'DESC'),
            page: $request->get('page', '1'),
            totalPerPage: $request->get('total_per_page', 15)
        ));
        return CastMemberResource::collection(collect($response->items))->additional([
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

    public function store(StoreCastMemberRequest $request, CreateCastMemberUseCase $useCase)
    {
        $input = new CastMemberCreateInputDto(
            name: $request->name,
            type: $request->type
        );
        $response = $useCase->execute($input);
        return (new CastMemberResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($id, ListCastMemberUseCase $useCase)
    {
        $response = $useCase->execute(new CastMemberListInputDto(id: $id));
        return (new CastMemberResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update(UpdateCastMemberRequest $request, $id, UpdateCastMemberUseCase $useCase)
    {
        $response = $useCase->execute(new CastMemberUpdateInputDto(
            id: $id,
            name: $request->name,
        ));
        return (new CastMemberResource($response))
            ->response()
            ->setStatusCode(200);
    }

    public function destroy($id, DeleteCastMemberUseCase $useCase)
    {
        $useCase->execute(new CastMemberDeleteInputDto(id: $id));
        return response()->noContent();
    }
}
