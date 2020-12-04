class CommentComponent {
  constructor() {
    this.View = {
      CommentsWrapper:$('#comments-wrapper')
    }

    this.State = {
      ReplySubmitInProgress:false
    };
    /**
     * Bind 'this' inside of all the class methods,
     * where 'this' keyword is used.
     */
    this.RenderReplies = this.RenderReplies.bind(this);
    /**
     * Set up the event listeners.
     */
    this.View.CommentsWrapper.delegate('.showHideReplies','click',this.ShowHideReplies.bind(this));
    this.View.CommentsWrapper.delegate('.btn-show-reply-form','click',this.ShowHideReplyForm.bind(this));
    this.View.CommentsWrapper.delegate('form','submit',this.SubmitReply.bind(this));
  }
  /**
   * Format the timestamps, to be more user friendly.
   * 
   * @param {String} timestamp
   * @return {String}
   */
  FormatTimestamp(timestamp) {
    const [withoutTimezone] = timestamp.split('.');
    const [date,time] = withoutTimezone.split('T');

    return `${time} ${date.split('-').reverse().join('/')}`;
  }
  /**
   * Render all replies.
   * 
   * @param {Array} replies
   * @return {String}
   */
  RenderReplies(replies) {
    return replies.map(reply => {
      return `
        <div class="comment-wrapper">
        <h3>By : ${reply.name} (${this.FormatTimestamp(reply.created_at)})</h3>
        <h2>${reply.content}</h2>
        <a href="#" class="showHideReplies" data-comment-id="${reply.id}">View replies</a>
        <div id="replies-wrapper-${reply.id}">

        </div>
      </div>
      `
    }).join('')
  }
  /**
   * Render the submit reply form.
   * 
   * @param {Number} commentID
   * @return {String}
   */
  RenderReplyForm(commentID) {
    const className = 'text-sm rounded-md border-gray-300';

    return `
      <button
        id="btn-show-reply-form-${commentID}"
        class="btn-show-reply-form"
        data-comment-id="${commentID}"
      >
        Reply
      </button>
      <form id="reply-form-${commentID}" class="reply-form" action="/addReply" method="POST" style="display:none">
        <input type="hidden" name="comment_id" value="${commentID}"/>
        <input class="${className}" type="text" name="name" placeholder="Name" />
        <br/>
        <input class="${className}" type="text" name="email" placeholder="Email" />
        <br/>
        <textarea name="content"></textarea>
        <br/>
        <button type="submit">Submit</button>
      </form>
    `;
  }
  /**
   * Validate the reply json object.
   * Return the error string, if one exists.
   * 
   * @return {String|undefined}
   */
  ValidateForm(json) {
    for ( const [key,value] of Object.entries(json) ) {
      if ( !value ) {
        return `${key} is required`;
      }
    }
  }
  /****************
   * Event Handlers
   ***************/
  /**
   * Show and hide reply wrappers.
   * 
   * @param {Object} event
   * @return {void}
   */
  async ShowHideReplies(event) {
    event.preventDefault();

    try {
      const showHideRepliesButton = $(event.target);
      const commentID = showHideRepliesButton.attr('data-comment-id');
      const repliesWrapper = $('#replies-wrapper-' + commentID);
      /**
       * If the reply button is already pressed, just
       * hide the reply wrappers.
       */
      if ( Number(showHideRepliesButton.attr('data-active')) === 1 ) {
        repliesWrapper.hide();

        return showHideRepliesButton.attr('data-active',0).text('Show replies');
      }

      showHideRepliesButton.attr('data-active',1).text('Hide replies');
      /**
       * If the replies are already fetch via HTTP, just
       * show the reply wrappers
       */
      if ( Number(showHideRepliesButton.attr('data-is-loaded')) === 1 ) { return repliesWrapper.hide(); }
      /**
       * Fetch the replies from the server and render them.
       */
      const response = await axios.get('/getReplies',{
        params:{ comment_id:commentID}
      });

      if ( !response.data.success ) {
        throw new Error(response.data.message);
      }

      repliesWrapper.html(`
        <div id="reply-wrapper-${commentID}">
          ${this.RenderReplies(response.data.data)}
        </div>
        <div>
          ${this.RenderReplyForm(commentID)}
        </div>
      `);

      showHideRepliesButton.attr('data-is-loaded',1);

    } catch(e) {
      return alert(e.message);
    }
  }
  /**
   * Show and hide the reply form
   * 
   * @param {Object} event
   * @return {void}
   */
  ShowHideReplyForm(event) {
    event.preventDefault();

    const showHideReplyFormButton = $(event.target);
    const form = $('#reply-form-' + showHideReplyFormButton.attr('data-comment-id'));
    const isActive = Number(showHideReplyFormButton.attr('data-active')) === 1;
    /**
     * If the form is showing, then hide it
     * and set the active status to zero.
     * Otherwise, show and set to one.
     */
    form[isActive ? 'hide' : 'show']();
    showHideReplyFormButton.attr('data-active',isActive ? 0 : 1);
  }
  /**
   * Submit a new reply.
   * 
   * @param {Event} event
   * @return {void}
   */
  async SubmitReply(event) {
    event.preventDefault();

    if ( this.State.ReplySubmitInProgress ) { return; }

    this.State.ReplySubmitInProgress = true;

    try {
      const form = $(event.target);
      const data = form.serializeObject();
      const errorMessage = this.ValidateForm(data);

      if ( errorMessage ) {
        throw new Error(errorMessage);
      }

      await axios.post(form.attr('action'),data);
      /**
       * Insert a temporary reply, waiting to be aproved.
       */
      $('#reply-wrapper-' + data.comment_id).append(`
        <div class="comment-wrapper">
          <h3>By : ${data.name}</h3>
          <h2>${data.content}</h2>
        </div>
      `);
      /**
       * Hide the submit form.
       */
      $(`#btn-show-reply-form-${data.comment_id}`).trigger('click');
      /**
       * Clear the form fields
       */
      form.find('input[type=text], textarea').val('');
    } catch(e) {
      alert(e.message);
    } finally {
      this.State.ReplySubmitInProgress = false;
    }
  }
  /********************
   * End Event Handlers
   *******************/
}
/**
 * Initialize the component
 */
$(document).ready(function() {
  new CommentComponent();
});