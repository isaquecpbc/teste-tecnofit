<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Movement as MovementResource;

/**
 * @OA\Schema(
 *     schema="MovementResource",
 *     type="object",
 *     title="Movement Resource",
 *     required={"name"},
 *     properties={
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="BoJack Horseman"),
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="MovementStoreRequest",
 *     type="object",
 *     title="Movement Store Request",
 *     required={"name"},
 *     properties={
 *         @OA\Property(property="name", type="string", example="BoJack Horseman"),
 *     }
 * ) 
 * 
 */
class MovementController extends BaseController
{

    /**
     * @OA\Get(
     *     path="/api/movements",
     *     summary="Get list of movements",
     *     tags={"Movements"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Movement"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function index()
    {
        $movements = Movement::get();

        return $this->sendResponse(MovementResource::collection($movements), 'Movements Retrieved Successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * @OA\Post(
     *     path="/api/movements",
     *     summary="Create a new movement",
     *     tags={"Movements"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MovementStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Movement")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();
    
        $validator = Validator::make($input, [
            'name'              => ['required', 'string', 'max:250'],
        ]);
    
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
    
        $movement = Movement::create($input);
    
        return $this->sendResponse(new MovementResource($movement), 'Movement Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movement = Movement::findOrFail($id);
  
        if (is_null($movement)) {
            return $this->sendError('Movement not found.');
        }
   
        return $this->sendResponse(new MovementResource($movement), 'Movement Retrieved Successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/movements/{id}",
     *     summary="Update an existing movement",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the movement to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name'              => ['required', 'string', 'max:250'],
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $movement = Movement::findOrFail($id);
          
        $movement->name             = $input['name'];
        $movement->save();
   
        return $this->sendResponse(new MovementResource($movement), 'Movement Updated Successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/movements/{id}",
     *     summary="Delete a movement",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the movement to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function destroy($id)
    {
        $movement = Movement::findOrFail($id);

        $movement->delete();
   
        return $this->sendResponse([], 'Movement Deleted Successfully.');
    }
}