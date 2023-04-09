<?php

namespace App\Http\Controllers;

use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class ItemsController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $offest = $request->page * 10;
        $searchQuery = $request->searchQuery;
        $product_id = $request->productid;
        $sqlQuery = "select * from items where product_id = ? ";
        if($searchQuery != ""){
            $sqlQuery = $sqlQuery ."and serial_number like '%". $searchQuery ."%' ";
        }
        ;
        $sqlQuery = $sqlQuery."ORDER by id DESC LIMIT 10 OFFSET ". $offest ;
       
        $results = DB::select($sqlQuery, [$product_id]);
        return response()->json([
            'status' => 'success',
            'data' => $results,
           
        ], 201);
    }

  
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric|max_digits:11',
            'serial_number' => 'required|string|max:50',
            'sold' => 'boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

     

        $Item = Items::create([
            'product_id' =>  $request->input('product_id'),
            'serial_number' => $request->input('serial_number'),
            'sold' => $request->input('sold'),
         
        ]);
      
      return response()->json([
        'status' => 'success',
        'data' => $Item,
       
        ], 201);
    }

   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
      
            'serial_number' => 'string|max:50',
            'sold' => 'boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

     
        
        $Item = Items::find($id);
        if ($Item){
            if ($request->input('serial_number')) {
                $Item->serial_number = $request->input('serial_number');
            }
            if ($request->input('sold')) {
                $Item->sold = $request->input('sold');
            }
          
            $Item->save();
          
          return response()->json([
            'status' => 'success',
            'data' => $Item,
           
        ], 201);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'Couldnt update',
               
            ], 201);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Item = Items::find($id);
        
        if($Item){
            $Item->delete();
            return response()->json([
               'status' => 'success',
               'data' => $Item,
              
           ], 201);
        }else{
            return response()->json([
                'status' => 'error',
                'data' => "Coudn't delete",
               
            ], 201);
        }
        
    }
}
