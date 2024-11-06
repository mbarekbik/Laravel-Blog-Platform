<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category-list', ['only' => ['index']]);
        $this->middleware('permission:category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        if ($request->input('q') !== null) {
            $terms = $request->input('q');
        } else {
            $terms = '';
        }
        if ($request->input('order') !== null) {
            $order = $request->input('order');
            $orderString = $order;
            $orderVar = str_contains($order, "Alphabetical") ? 'name' : 'id';
            $order = str_replace('Alphabetical', '', $order);
        } else {
            $order = 'desc';
            $orderString = $order;
            $orderVar = 'id';
        }
        if ($request->input('limit') !== null) {
            $limit = $request->input('limit');
        } else {
            $limit = 20;
        }

        $categories = Category::withCount('posts')->orderBy($orderVar, $order);

        if ($terms !== null && $terms !== '') {
            $keywords = explode(' ', $terms);

            $categories->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'like', '%' . $keyword . '%')
                        ->orWhere('backgroundColor', 'like', '%' . $keyword . '%')
                        ->orWhere('textColor', 'like', '%' . $keyword . '%');
                }
            });
        }

        if ($limit === 0) {
            $categories = $categories->get();
        } else {
            $categories = $categories->paginate($limit);
        }
        return view('category.index', [
            'categories' => $categories,
            'terms' => $terms,
            'order' => $orderString,
            'limit' => $limit,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories,name',
            'backgroundColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'textColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
        ]);

        $input = $request->all();

        Category::create($input);

        return redirect()->route('categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit(int $id)
    {
        $category = Category::findOrFail($id);

        return view('category.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'backgroundColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'textColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
        ]);

        $category = Category::findOrFail($id);

        $input = $request->all();

        $category->update($input);

        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return redirect()->back();
    }
}
