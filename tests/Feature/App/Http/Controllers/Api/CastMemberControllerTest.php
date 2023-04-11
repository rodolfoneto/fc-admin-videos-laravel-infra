<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CastMemberController;
use App\Http\Requests\{
    StoreCastMemberRequest,
    UpdateCastMemberRequest,
};
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Exception\{
    EntityValidationException,
    NotFoundException,
};
use Core\Domain\Repository\CastMemberRepositoryInterface;
use App\Models\{
    CastMember as Model,
};
use Core\UseCase\CastMember\{CreateCastMemberUseCase,
    DeleteCastMemberUseCase,
    ListCastMembersUseCase,
    ListCastMemberUseCase,
    UpdateCastMemberUseCase};
use Illuminate\Http\{
    JsonResponse,
    Request,
    Response,
};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CastMemberControllerTest extends TestCase
{
    protected CastMemberRepositoryInterface $repository;

    public function test_index()
    {
        $useCase = new ListCastMembersUseCase($this->repository);
        $controller = new CastMemberController();
        $response = $controller->index(new Request(), $useCase);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function test_store()
    {
        $request = new StoreCastMemberRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => "new cat",
            'type' => 1
        ]));
        $useCase = new CreateCastMemberUseCase($this->repository);
        $controller = new CastMemberController();
        $response = $controller->store($request, $useCase);
        $data = $response->getData()->data;
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertDatabaseHas('cast_members', [
            'id' => $data->id,
            'type' => $data->type,
            'name' => $data->name,
            'created_at' => $data->created_at,
        ]);
    }

    public function test_store_invalid_param()
    {
        $request = new StoreCastMemberRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => "n",
            'type' => 1
        ]));
        $useCase = new CreateCastMemberUseCase($this->repository);
        $controller = new CastMemberController();
        $this->expectException(EntityValidationException::class);
        $controller->store($request, $useCase);
    }

    public function test_update_invalid_id()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $request = new UpdateCastMemberRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => "name",
            'type' => 1
        ]));
        $controller = new CastMemberController();
        $useCase = new UpdateCastMemberUseCase($this->repository);
        $this->expectException(NotFoundException::class);
        $controller->update($request, $uuid, $useCase);
    }

    public function test_update_invalid_param()
    {
        $model = Model::factory()->create();
        $uuid = $model->id;
        $request = new UpdateCastMemberRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => "n",
            'type' => 1
        ]));
        $controller = new CastMemberController();
        $useCase = new UpdateCastMemberUseCase($this->repository);
        $this->expectException(EntityValidationException::class);
        $controller->update($request, $uuid, $useCase);
    }

    public function test_update()
    {
        $model = Model::factory()->create();
        $uuid = $model->id;
        $request = new UpdateCastMemberRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => "updated",
            'type' => 1
        ]));
        $controller = new CastMemberController();
        $useCase = new UpdateCastMemberUseCase($this->repository);
        $response = $controller->update($request, $uuid, $useCase);
        $data = (array) $response->getData()->data;
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertDatabaseHas('cast_members', $data);
    }

    public function test_show()
    {
        $useCase = new ListCastMemberUseCase($this->repository);
        $model = Model::factory()->create();
        $id = $model->id;
        $controller = new CastMemberController();
        $response = $controller->show($id, $useCase);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function test_show_not_found()
    {
        $useCase = new ListCastMemberUseCase($this->repository);
        $controller = new CastMemberController();
        $this->expectException(NotFoundException::class);
        $controller->show('FAKE_ID', $useCase);
    }

    public function test_destroy_invalid_id()
    {
        $useCase = new DeleteCastMemberUseCase($this->repository);
        $controller = new CastMemberController();
        $this->expectException(NotFoundException::class);
        $controller->destroy('FAKE_ID', $useCase);
    }

    public function test_destroy()
    {
        $model = Model::factory()->create();
        $useCase = new DeleteCastMemberUseCase($this->repository);
        $controller = new CastMemberController();
        $resource = $controller->destroy($model->id, $useCase);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $resource->getStatusCode());
        $this->assertSoftDeleted($model);
    }

    protected function setUp(): void
    {
        $this->repository = new CastMemberEloquentRepository(new Model());
        parent::setUp();
    }
}
