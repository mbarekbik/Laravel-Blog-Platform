<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdminPostCard extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $post;

    public $countHighlighted;

    public function __construct($post, $countHighlighted)
    {
        $this->post = $post;
        $this->countHighlighted = $countHighlighted;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin-post-card');
    }
}
