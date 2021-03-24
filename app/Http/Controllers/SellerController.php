<?php

namespace App\Http\Controllers;

use App\Repositories\SellerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $validator = Validator::make($request->all(), [
            'cnpj' => 'required|string|min:14|max:14|unique:sellers,cnpj',
            'id_user' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->responseErrorClient($validator->errors()->toArray());
        }


        return response()->json($this->seller->create($request->all()));
    }

}