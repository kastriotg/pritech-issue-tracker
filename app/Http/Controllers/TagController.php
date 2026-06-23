<?php

namespace App\Http\Controllers;

use App\Actions\Tags\CreateTagAction;
use App\Actions\Tags\ListTagsAction;
use App\Http\Requests\StoreTagRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ListTagsAction $listTags): View
    {
        return view('tags.index', $listTags->handle($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request, CreateTagAction $createTag): RedirectResponse
    {
        $createTag->handle($request->validated());

        return to_route('tags.index')
            ->with('status', 'Tag created.');
    }
}
