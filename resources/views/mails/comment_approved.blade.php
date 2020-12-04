<h1>Hello {{ $commentAuthor ? $commentAuthor->name : $comment->name }}</h1>

@if ( $commentAuthor )
  <h1>A reply to your comment has been posted!</h1>
  <p>Original Comment : {{ $commentAuthor->content }}</p>
@else
  <p>Your comment has been approved!</p>
@endif

<p>Comment left : {{ $comment->content }}</p>