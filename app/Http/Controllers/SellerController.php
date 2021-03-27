<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidSellerTypeException;
use App\Repositories\SellerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class SellerController extends Controller
{

    /**
     * @var SellerRepositoryInterface
     */
    protected $seller;

    /**
     * Create a new controller instance.
     *
     * @param SellerRepositoryInterface $seller
     * @return void
     */
    public function __construct(SellerRepositoryInterface $seller)
    {
        $this->seller = $seller;
    }

    /**
     * Store seller.
     *
     * @param Request $request
     * @return array
     */
    public function store(Request $request): JsonResponse
    {

        try {
            $validator = Validator::make($request->all(), [
                        'cnpj' => 'required|string|min:14|max:14|unique:sellers,cnpj',
                        'id_user' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return $this->responseErrorClient($validator->errors()->toArray());
            }

            return response()->json($this->seller->create($request->all()));
        } catch (InvalidSellerTypeException $exc) {
            return $this->responseErrorClient([
                        'id_user' => [$exc->getMessage()]
            ]);
        } catch (\Exception $exc) {
            return $this->responseErrorServer([
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => 'Unknown server error'
            ]);
        }
    }

}