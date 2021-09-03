<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $user = request()->user();
        return ProductResource::collection($user->products()->paginate(10));
    }

    public function show(Product $product)
    {
        abort_if(request()->user()->cannot('view', $product), 403);
        return new ProductResource($product);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['photo'] = $request->file('photo')->store('');
        Product::create($data);

        return response(['msg' => __('Created Successfully')], 201);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->all();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('');
        }
        $product->update($data);

        return response(['msg' => __('Updated Successfully')]);
    }

    public function destroy(Product $product)
    {
        abort_if(request()->user()->cannot('delete', $product), 403);
        $product->delete();

        return response(['msg' => __('Deleted Successfully')]);
    }
}
