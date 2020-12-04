<div class="text-lg max-w-screen-lg mx-auto">
    <form action="{{ route('addComment') }}" method="POST" class="space-y-8">
        @csrf       
        <div class="space-y-8 divide-y divide-gray-200">
            <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <input type="hidden" name="belongs_to_id" value="{{ isset($post) ? $post->id : $article->id }}">
                <input type="hidden" name="belongs_to" value="{{ $belongs_to }}">
                <div class="sm:col-span-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Name
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="text" id="name" name="name" value="{{ old('name') ?? '' }}" class="border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-10 focus:outline-none sm:text-sm rounded-md">
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="text" id="email" name="email" value="{{ old('email') ?? '' }}" class="border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-10 focus:outline-none sm:text-sm rounded-md">
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label for="content" class="block text-sm font-medium text-gray-700">
                        Content
                    </label>
                    <div class="mt-1">
                        <textarea id="content" name="content" rows="10" class="border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm w-8/12 sm:text-sm border-gray-300 rounded-md">
                            {{ old('content') ?? '' }}
                        </textarea>
                    </div>
                </div>

            </div>
        </div>

        <div class="pt-5">
            <div class="flex justify-end">
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save
                </button>
            </div>
        </div>
    </form>
</div>

<div class="text-lg max-w-screen-lg mx-auto">
    <div id="comments-wrapper">
        @if(count($comments) === 0)
            <h1>No comments yet</h1>         
        @else
            @foreach($comments as $comment)
                <div class="comment-wrapper">
                    <h3>By : {{ $comment->name }} ({{ $comment->created_at->format('d/m/Y') }})</h3>
                    <h2>{{ $comment->content }}</h2>
                    <a href="#" class="showHideReplies" data-comment-id="{{ $comment->id }}">Show replies</a>
                    <div id="replies-wrapper-{{ $comment->id }}">

                    </div>
                </div>
            @endforeach
        @endif
    </div>
    {{ $comments->links() }}
</div>

<script src="{{ asset('js/comments.js')}}"></script>
<link href="{{ asset('/css/comments.css') }}" rel="stylesheet">