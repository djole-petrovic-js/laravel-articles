<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Comments;

class CommentApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $comment;
    public $commentAuthor = null;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Comments $comment, Comments $commentAuthor = null)
    {
        $this->comment = $comment;
        $this->commentAuthor = $commentAuthor;
    }
    /**
     * Set up the notification email to be sent to the
     * user who posted a comment, that his comment has
     * been approved.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('noreply@app.com')
            ->view('mails.comment_approved');
    }
}
