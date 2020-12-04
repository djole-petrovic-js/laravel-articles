<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentFormRequest;
use App\Models\Comments;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommentApproved;

class CommentsController extends Controller
{
  /**
   * Show the paginated comments
   */
  public function index()
  {
    $comments = Comments::latest()->paginate(10);

    return view('comments.index', [
      'comments' => $comments
    ]);
  }
  /**
   * Show only one comment
   * 
   * @param int $id
   */
  public function view(int $id) {
    $comment = Comments::findOrFail($id);

    return view('comments.view', [
      'comment' => $comment
    ]);
  }
  /**
   * Method for saving a comment
   * 
   * @param CommentFormRequest $request
   * @param int $id
   * @return RedirectResponse
   */
  public function store(CommentFormRequest $request, int $id)
  {
    $comment = new Comments();
    /**
     * Approved flag should be zero or one.
     */
    $approved = (int)$request->approved === 1 ? 1 : 0;

    $comment = Comments::findOrFail($id);

    $comment->name = $request->name;
    $comment->email = $request->email;
    $comment->content = $request->content;
    $comment->approved = $request->approved;
    
    $comment->save();

    return redirect()->route('comments.index')->with('success', 'Comment saved!');
  }
  /**
   * Method for showing the editing form
   * 
   * @param int $id
   * @return View
   */
  public function edit($id)
  {
    $comment = Comments::findOrFail($id);

    return view('comments.edit', [
      'comment' => $comment
    ]);
  }
  /**
   * Method for deleting a comment completly.
   * 
   * @param int $id
   * @return RedirectResponse
   */
  public function destroy(int $id)
  {
    Comments::destroy($id);

    return redirect()->route('comments.index')->with('success', 'Comment Deleted!');
  }
  /**
   * Method for making a comment public.
   * Notification mail will be sent to the publisher of the comment
   * 
   * @param int $id
   * @return RedirectResponse
   */
  public function approve(int $id)
  {
    $comment = Comments::findOrFail($id);
    /**
     * Check if this comment has already been approved.
     */
    if ( $comment->approved_email_sent ) {
      return redirect()->route('comments.index')->with('error', 'Comment already approved!');
    }
    /**
     * Set the flags and send the email.
     */
    $comment->approved = 1;
    $comment->approved_email_sent = 1;

    $comment->save();
    /**
     * Send a notification to the user who created the comment.
     */
    Mail::to($comment->email)->send(new CommentApproved($comment));
    /**
     * If comment being aproved is a reply, than the user who
     * posted the initial comment should be notified
     */
    if ( $comment->comment_id ) {
      $commentAuthor = Comments::findOrFail($comment->comment_id);

      Mail::to($commentAuthor->email)->send(new CommentApproved($comment,$commentAuthor));
    }

    return redirect()->route('comments.index')->with('success', 'Comment approved!');
  }
}