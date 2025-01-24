<?php

namespace App\Http\Controllers;

use App\Models\PersonalRecord;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\PersonalRecord as PersonalRecordResource;

/**
 * @OA\Schema(
 *     schema="PersonalRecordResource",
 *     type="object",
 *     title="Personal Record Resource",
 *     required={"value", "dt_record", "user_id", "movement_id"},
 *     properties={
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="user_id", type="integer", example1),
 *         @OA\Property(property="movement_id", type="integer", example=1),
 *         @OA\Property(property="value", type="float", example=72.4),
 *         @OA\Property(property="dt_record", type="date", example="2024-12-25 00:00:00"),
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="PersonalRecordStoreRequest",
 *     type="object",
 *     title="Personal Record Store Request",
 *     required={"value", "dt_record", "user_id", "movement_id"},
 *     properties={
 *         @OA\Property(property="user_id", type="integer", example=1),
 *         @OA\Property(property="movement_id", type="integer", example=1),
 *         @OA\Property(property="value", type="float", example=72.4),
 *         @OA\Property(property="dt_record", type="date", example="2024-12-25 00:00:00"),
 *     }
 * ) 
 * 
 */
class PersonalRecordController extends BaseController
{

    /**
     * @OA\Get(
     *     path="/api/personal-records",
     *     summary="Get list of personalRecords",
     *     tags={"PersonalRecords"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PersonalRecord"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function index()
    {
        $personalRecords = PersonalRecord::get();

        return $this->sendResponse(PersonalRecordResource::collection($personalRecords), 'Personal Records Retrieved Successfully.');
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
     *     path="/api/personal-records",
     *     summary="Create a new personalRecord",
     *     tags={"Personal Records"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PersonalRecordStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PersonalRecord")
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
            'user_id'       => ['required', 'exists:users,id'],
            'movement_id'   => ['required', 'exists:movements,id'],
            'value'         => ['required', 'numeric', 'min:10'],
            'date'          => ['required', 'date'],
        ]);
    
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
    
        $personalRecord = PersonalRecord::create($input);
    
        return $this->sendResponse(new PersonalRecordResource($personalRecord), 'Personal Record Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $personalRecord = PersonalRecord::findOrFail($id);
  
        if (is_null($personalRecord)) {
            return $this->sendError('Personal Record not found.');
        }
   
        return $this->sendResponse(new PersonalRecordResource($personalRecord), 'Personal Record Retrieved Successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/personal-records/ranking/list",
     *     summary="Get ranking of movements",
     *     tags={"PersonalRecords"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="movement_id",
     *         in="query",
     *         required=false,
     *         description="Filter by movement ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         description="Filter by movement name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PersonalRecordResource"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function ranking(Request $request)
    {
        $query = PersonalRecord::with(['user', 'movement']);

        // Filtro por movement_id
        if ($request->has('movement_id')) {
            $query->where('movement_id', $request->input('movement_id'));
        }

        // Filtro por nome do movimento
        if ($request->has('name')) {
            $query->whereHas('movement', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('name') . '%');
            });
        }

        $personalRecords = $query->get();

        // Agrupar dados de movimento e calcular o recorde pessoal
        $ranking = $personalRecords->groupBy('movement_id')->map(function($records) {
            $movement = $records->first()->movement;
            $userDetails = $records->groupBy('user_id')->map(function($userRecords) {
                $highestRecord = $userRecords->sortByDesc('value')->first();
                return [
                    'user_id'       => $highestRecord->user_id,
                    'user_name'     => $highestRecord->user->name,
                    'movement_id'   => $highestRecord->movement->id,
                    'movement_name' => $highestRecord->movement->name,
                    'value'         => $highestRecord->value,
                    'date'          => $highestRecord->dt_record,
                ];
            });

            return $userDetails->values()->toArray();
        });
        
        // Ordenar o ranking e atribuir posições
        $result = [];
        foreach ($ranking as $movementId => $movementRecord) {
            $sortedRecords = collect($movementRecord)->sortByDesc('value')->values();
            $position = 1;
            $prevValue = null;
            
            foreach ($sortedRecords as $i => $record) {
                if ($prevValue !== $record['value']) {
                    $position = $i + 1;
                }
                
                // atribuir movementRecord ao resutado
                $result[] = [
                    'movement_name' => $record['movement_name'],
                    'ranking'       => [
                        'position'  => $position,
                        'user'      => $record['user_name'],
                        'value'     => $record['value'],
                        'date'      => $record['date'],
                    ]
                ];
                $prevValue = $record['value'];
            }
        }

        return $this->sendResponse($result, 'Ranking Retrieved Successfully.');
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
     *     path="/api/personal-records/{id}",
     *     summary="Update an existing personalRecord",
     *     tags={"Personal Records"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the personalRecord to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
    *         @OA\Property(property="value", type="float", example=72.4),
    *         @OA\Property(property="dt_record", type="date", example="2024-12-25 00:00:00"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PersonalRecord")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Personal Record not found"
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
            'value'             => ['required', 'float', 'min:1'],
            'date'              => ['required', 'datetime'],
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $personalRecord = PersonalRecord::findOrFail($id);
          
        $personalRecord->value          = $input['value'];
        $personalRecord->nadateme       = $input['date'];
        $personalRecord->save();
   
        return $this->sendResponse(new PersonalRecordResource($personalRecord), 'Personal Record Updated Successfully.');
    }

    
    /**
     * @OA\Delete(
     *     path="/api/personal-records/{id}",
     *     summary="Delete a personalRecord",
     *     tags={"PersonalRecords"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the personalRecord to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Personal Record deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Personal Record not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function destroy($id)
    {
        $personalRecord = PersonalRecord::findOrFail($id);

        $personalRecord->delete();
   
        return $this->sendResponse([], 'Personal Record Deleted Successfully.');
    }
}