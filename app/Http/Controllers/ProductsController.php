<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request,CategoryService $categoryService)
    {
        $builder = Product::query()->where('on_sale', true);

        if ($search = $request->input('search', '')) {
            $like = '%' . $search . '%';
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        if ($order = $request->input('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        if($request->input('category_id') && $category = Category::find($request->input('category_id'))){
            if($category->is_directory){
                $builder->whereHas('category',function($query) use ($category){
                    $query->where('path','like',$category->path.$category->id.'-%');
                });
            }else{
                $builder->where('category_id',$category->id);
            }
        }

        $products = $builder->paginate(16);
        return view('products.index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'order' => $order
            ],
            'category' => $category ?? null,
            'categoryTree' => $categoryService->getCategoryTree()
        ]);
    }

    public function show(Product $product,Request $request){
        if(!$product->on_sale){
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;
        if ($user = $request->user()){
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        $review = OrderItem::query()
            ->with(['order.user','productSku'])
            ->where('product_id',$product->id)
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at','desc')
            ->limit(10)
            ->get();

        return view('products.show',[
            'product' => $product,
            'favored' => $favored,
            'reviews' => $review,
        ]);
    }

    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disfavor(Product $product,Request  $request){
        $user = $request->user();
        $user->favoriteProducts()->detach($product);
        return [];
    }

    public function favorites(Request $request){
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites',[
            'products' => $products
        ]);
    }
}
