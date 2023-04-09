<?php

namespace App\Http\Controllers;
use App\Models\ProductsTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class ProductsTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $offest = $request->page * 10;
        $searchQuery = $request->searchQuery;
        $sqlQuery = "select U.*, (Select count(*) from items where product_id = U.id and sold = 0) as Count from products_types U where user_id = ? ";
        if($searchQuery != ""){
            $sqlQuery = $sqlQuery ."and title like '%". $searchQuery ."%' ";
        }
        ;
       // for pagination $sqlQuery = $sqlQuery."ORDER by id DESC LIMIT 10 OFFSET ". $offest ;
       $sqlQuery = $sqlQuery."ORDER by id DESC";
        $results = DB::select($sqlQuery, [$request->user->ID]);
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
            'title' => 'required|string|max:150',
            'description' => 'required|string|max:300',
            'picture' => 'required|image',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        
        $path = $request->picture->store('productsImages');
       
        $Product = ProductsTypes::create([
            'user_id' => $request->user->ID,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'picture' => $path,
        ]);
      
      return response()->json([
        'status' => 'success',
        'data' => $Product,
       
    ], 201);
    }

   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:150',
            'description' => 'string|max:300',
            'picture' => 'image',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        
        
        

        $Product = ProductsTypes::where([
            'user_id' => $request->user->id,
            'id' => $id])->first();;
        if ($Product){
            if ($request->input('title')) {
                $Product->title = $request->input('title');
            }
            if ($request->input('description')) {
                $Product->description = $request->input('description');
            }
           
            
            if ($request->hasFile('picture')) {
                $path = $request->picture->store('productsImages');
                $Product->picture = $path;
            }
            $Product->save();
          
          return response()->json([
            'status' => 'success',
            'data' => $Product,
           
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
    public function destroy( string $id)
    {
        $Product = ProductsTypes::where(['id' => $id])->first();
        error_log("delete");
        if($Product){
            $Product->delete();
            return response()->json([
               'status' => 'success',
               'data' => $Product,
              
           ], 201);
        }else{
            return response()->json([
                'status' => 'error',
                'data' => "Coudn't delete",
               
            ], 400);
        }
        
    }
}
