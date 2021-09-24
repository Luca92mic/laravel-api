<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Post;
use App\Category;
use App\Tag;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        $categories = Category::all();
        return view('admin.posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories','tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->tags);

        //validate
        $request->validate([
            'title' => 'required|max:60',
            'content' => 'required'
        ]);

        $data = $request->all();

        // fare nuova istanza
        $new_post = new Post();

        $slug = Str::slug($data['title'], '-');

        // duplicato eventuale
        $slug_base = $slug;

        $slug_presente = Post::where('slug', $slug)->first();

        $contatore = 1;
        while ($slug_presente) {
            $slug = $slug_base . '-' . $contatore;

            $slug_presente = Post::where('slug', $slug)->first();

            $contatore++;
        }
        // end duplicato

        $new_post->slug = $slug;
        $new_post->fill($data);

        // save
        $new_post->save();

        $new_post->tags()->attach($request->tags);

        return redirect()->route('admin.posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //slug
    public function show($slug)
    {
        $post = Post::where('slug',$slug)->first();
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //validate
        $request->validate([
            'title' => 'required|max:60',
            'content' => 'required'
        ]);

        $data = $request->all();

        // ricalcolo slug
        if($data['title'] != $post->title){

            $slug = Str::slug($data['title'], '-');
            $slug_base = $slug;

            //
            $slug_exist = Post::where('slug', $slug)->first();

            $contatore = 1;
            while($slug_exist){

                $slug = $slug_base . '-' . $contatore;

                $slug_exist = Post::where('slug', $slug)->first();

                $contatore++;
            }

            $data['slug'] = $slug;
        }

        $post->update($data);
        $post->tags()->sync($request->tags);

        return redirect()->route('admin.posts.index')->with('updated', 'Hai modificato con successo l\'elemento ' . $post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->tags()->detach();
        $post->delete();
        
        return redirect()->route('admin.posts.index');
    }
}
