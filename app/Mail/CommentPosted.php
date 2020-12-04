<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Comments;
use App\Models\User;

class CommentPosted extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $comment;
    public $article;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Comments $comment, $article)
    {
        $this->user = $user;
        $this->comment = $comment;
        $this->article = $article;
    }
    /**
     * Set up the notification email to be sent to the author
     * about the approved comment.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('noreply@app.com')
            ->view('mails.comment_posted');
    }
}
