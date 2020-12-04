<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="p-6 bg-white border-b border-gray-200">
            <h2>By : {{ $comment->name }}</h2>
            <h3>Email : {{ $comment->email }}</h3>
            <p>Content {{ $comment->content }}</p>
          </div>
        </div>
    </div>
</x-app-layout>