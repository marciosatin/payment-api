<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use function response;

class UserController extends Controller
{

    /**
     * @var UserRepositoryInterface
     */
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @param UserRepositoryInterface $user
     * @return void
     */
    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    /**
     * List users.
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request): JsonResponse
    {
        try {
            return response()->json($this->user->list($request->get('q')));
        } catch (\Exception $exc) {
            return $this->responseErrorServer([
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => 'Unknown server error'
            ]);
        }
    }

    /**
     * Show a specific user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $validator = Validator::make(['id' => $id], [
                        'id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return $this->responseErrorClient($validator->errors()->toArray());
            }

            return response()->json($this->user->show($id));
        } catch (ModelNotFoundException $e) {
            return $this->responseErrorClient([
                        'user' => ['User not found']],
                            Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->responseErrorServer([
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => 'Unknown server error'
            ]);
        }
    }

    /**
     * Store user.
     *
     * @param Request $request
     * @return array
     */
    public function store(Request $request): JsonResponse
    {

        try {
            $validator = Validator::make($request->all(), [
                        'full_name' => 'required|string',
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required|string|min:6',
                        'cpf' => 'required|string|min:11|max:11|unique:users,cpf',
                        'id_type' => 'required|exists:users_types,id'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorClient($validator->errors()->toArray());
            }

            return response()->json($this->user->create($request->all()));
        } catch (\Exception $exc) {
            return $this->responseErrorServer([
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => 'Unknown server error'
            ]);
        }
    }

}