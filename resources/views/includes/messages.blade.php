@if ($errors->any())
    <div class="text-lg max-w-screen-lg mx-auto">
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
@if(Session::has('comment_status'))
    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('comment_status') }}</p>
@endif