<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CommentCard extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $comment;

    public $post;

    public function __construct($comment, $post)
    {
        $this->comment = $comment;
        $this->post = $post;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.comment-card');
    }
}
