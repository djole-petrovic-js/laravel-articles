<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\News;
use App\Models\Post;
use Illuminate\Validation\Rule;

class Comments extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'content',
        'approved'
    ];
    /**
     * Get the validation rules
     * 
     * @return array
     */
    public static function Rules() {
        return [
            'name' => 'bail|required|alpha',
            'email' => 'bail|required|email',
            'content' => 'required|between:5,500',
            'belongs_to' => Rule::in(['News','Post']),
        ];
    }
    /**
     * Set the relation to the News model
     * 
     * @return $this
     */
    public function News()
    {
        return $this->belongsTo(News::class, 'belongs_to_id');
    }
    /**
     * Set the relation to the Post model
     * 
     * @return $this
     */
    public function Post()
    {
        return $this->belongsTo(Post::class, 'belongs_to_id');
    }
    /**
     * Fetch all the approved comments for the particular
     * article and for selected article model.
     * 
     * @param int $articleID
     * @param string $articleClass
     */
    public static function ApprovedComments($articleID, $articleClass)
    {
        return self::where([
            'belongs_to_id' => $articleID,
            'belongs_to' => $articleClass,
            'approved' => 1,
            'comment_id' => 0,
        ])->orderBy('created_at','desc')->paginate(1);
    }
    /**
     * Utility method for saving a new instance.
     * 
     * @param array $data
     * @return $this
     */
    public static function SaveInstance(array $data)
    {
        $instance = new Comments();

        foreach ( $data as $column => $value ) {
            $instance->{$column} = $value;
        }

        $instance->save();

        return $instance;
    }
}