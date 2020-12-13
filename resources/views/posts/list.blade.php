@extends('..layouts.app')

@section('content')
<section class="w-full bg-gray-200 py-4 flex-row justify-center text-center">
    <div class="flex justify-center">
        <div class="max-w-4xl">
            <h1 class="px-4 text-6xl break-words">List Post</h1>
        </div>
    </div>
</section>
<article class="w-full py-8">
    <div class="flex justify-center">
        <div class="max-w-7xl text-justify">@if($errors->any())
            <div class="w-full bg-red-500 p-2 text-center my-2 text-white">
                {{$errors->first()}}
            </div>
            @endif
            @if (session('status'))
                <div class="w-full bg-green-500 p-2 text-center my-2 text-white">
                    {{ session('status') }}
                </div>
            @endif
            <div class="text-right py-2">
                <a class="inline-block px-4 py-1 bg-orange-500 text-white rounded mr-2 hover:bg-orange-800" href="{{ route('posts.create') }}" title="Edit">Create new post</a>
            </div>
            <table class="table-auto">
                <thead>
                    <tr>
                        <th class="px-2">Title</th>
                        <th class="px-2">Creation</th>
                        <th class="px-2">Author</th>
                        <th class="px-2">Status</th>
                        <th class="px-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($posts as $post)
                    <tr>
                        <td class="px-2">{{ $post->title }}</td>
                        <td class="px-2">{{ $post->created_at->format('j F, Y') }}</td>
                        <td class="px-2">{{ $post->user->name }}</td>
                        <td class="px-2">
                            @if ($post->is_draft)
                                <div class="text-red-500">In draft</div>
                            @else
                                <div class="text-green-500">Published</div>
                            @endif
                        </td>
                        <td class="px-2">
                            <a class="inline-block px-4 py-1 bg-blue-500 text-white rounded mr-2 hover:bg-blue-800" href="{{ route('posts.edit', $post) }}" title="Edit">Edit</a>

                            <a class="inline-block px-4 py-1 bg-red-500 text-white rounded mr-2 hover:bg-red-800 delete-post" href="{{ route('posts.destroy', $post) }}" title="Delete" data-id="{{$post->id}}">Delete</a>
                            <form id="posts.destroy-form-{{$post->id}}" action="{{ route('posts.destroy', $post) }}" method="POST" class="hidden">
                                {{ csrf_field() }}
                                @method('DELETE')
                            </form>
                        </td>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</article>
<script>

    var delete_post_action = document.getElementsByClassName("delete-post");

    var deleteAction = function(e) {
        event.preventDefault();
        var id = this.dataset.id;
        if(confirm('Are you sure?')) {
            document.getElementById('posts.destroy-form-' + id).submit();
        }
        return false;
    }

    for (var i = 0; i < delete_post_action.length; i++) {
        delete_post_action[i].addEventListener('click', deleteAction, false);
    }
</script>
@endsection