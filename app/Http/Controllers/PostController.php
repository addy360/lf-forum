<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum'])
            ->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::withCount('comments')
            ->with(['author' => function ($q) {
                $q->select('id', 'username')->get();
            }])
            ->get();

        return response(["data" => $posts]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $clean_data = $request->validate([
            "title" => ['required'],
            "body" => ['required']
        ]);

        $clean_data['user_id'] = Auth::user()->id;;

        $created_post = Post::create($clean_data);

        return response([
            "message" => "Post was created successfully",
            "data" => $created_post
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $post = $post
            ->loadCount('comments')
            ->load(['comments.user' => function ($q) {
                $q->select('id', 'username');
            }])
            ->load(['author' => function ($q) {
                $q->select('id', 'username');
            }]);
        return response(['data' => $post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $clean_data = $request->validate([
            "title" => ['required'],
            "body" => ['required']
        ]);

        $post->update($clean_data);

        return response([
            "message" => "Post has been updated successfully",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response([
            "message" => "Post '{$post->title}' was deleted successfully"
        ]);
    }
}
