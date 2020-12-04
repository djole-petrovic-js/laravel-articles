<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Post;
use App\Models\Comments;
use Illuminate\Http\Request;
use App\Mail\CommentPosted;
use Illuminate\Support\Facades\Mail;

class FrontendController extends Controller
{
    public function home()
    {
        $posts = Post::latest()->take(4)->get();

        $news = News::latest()->take(4)->get();

        return view('home', compact('posts', 'news'));
    }

    public function posts()
    {
        $posts = Post::latest()->paginate(6);

        return view('posts', compact('posts'));
    }

    public function news()
    {
        $news = News::latest()->paginate(6);

        return view('news', compact('news'));
    }

    public function postShow($id)
    {
        $post = Post::findOrFail($id);
        $belongsTo = (new \ReflectionClass(Post::class))->getShortName();
        $comments = Comments::ApprovedComments($id,$belongsTo);

        return view('post', [
            'post' => $post,
            'comments' => $comments,
            'belongs_to' => $belongsTo,
        ]);
    }

    public function newsShow($id)
    {
        $article = News::findOrFail($id);
        $belongsTo = (new \ReflectionClass(News::class))->getShortName();
        $comments = Comments::ApprovedComments($id,$belongsTo);

        return view('article', [
            'article' => $article,
            'comments' => $comments,
            'belongs_to' => $belongsTo,
        ]);
    }
    /**
     * Method for adding a main comment (with no parent comment).
     * Author of the article will be notified that a new comment
     * has been posted.
     * 
     * @param Request $request
     */
    public function addComment(Request $request)
    {
        /**
         * User will be redirected, if the validation fails.
         */
        $request->validate(Comments::Rules());

        $comment = Comments::SaveInstance([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'content' => $request->input('content'),
            'belongs_to_id' => $request->input('belongs_to_id'),
            'belongs_to' => $request->input('belongs_to'),
        ]);

        $article = $comment->{$request->input('belongs_to')};
        /**
         * Send the notification mail to the author.
         */
        Mail::to($article->author->email)->send(new CommentPosted(
            $article->author,
            $comment,
            /**
             * Get the coresponding article class, 
             * either News or Post
             */
            $comment->{$request->input('belongs_to')}
        ));

        $request->session()->flash('comment_status', 'Comment saved. Wait for approval');
        /**
         * Form the coresponding redirect route, based on the
         * type of article.
         */
        $redirectRoute = strtolower($request->input('belongs_to')) . '.show';

        return redirect()->route($redirectRoute, ['id' => $request->input('belongs_to_id')]);
    }
    /**
     * Method for fetching all comments, that have the
     * comment_id set (which means they are considered replies)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getReplies(Request $request)
    {
        $response = ['success' => false,'message' => '','data' => []];

        $comment_id = $request->query('comment_id');

        if ( !is_string($comment_id) ) {
            $response['message'] = 'Bad request';

            return response()->json($response);
        }
        /**
         * Fetch all the approved replies.
         * If the $comment_id leads to a comment that does not exists,
         * then an empty array will be returned.
         */
        $response['data'] = Comments::where([
            'approved' => 1,
            'comment_id' => $comment_id,
        ])->orderBy('created_at','asc')->get();

        $response['success'] = true;

        return response()->json($response);
    }
    /**
     * Method for submiting a new reply
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function addReply(Request $request)
    {
        $response = ['success' => false,'message' => '','data' => []];

        $comment = Comments::findOrFail($request->input('comment_id'));
        /**
         * Processing will end, if the data is not valid.
         */
        $request->validate(Comments::Rules());

        $reply = Comments::SaveInstance([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'content' => $request->input('content'),
            'belongs_to_id' => $comment->belongs_to_id,
            'belongs_to' => $comment->belongs_to,
            'comment_id' => $comment->id
        ]);

        $article = $comment->{$comment->belongs_to};
        /**
         * Send the notification mail to the author.
         */
        Mail::to($article->author->email)->send(new CommentPosted(
            $article->author,
            $reply,
            $article
        ));

        $response['data'] = $reply;
        $response['success'] = true;

        return response()->json($response);
    }
}