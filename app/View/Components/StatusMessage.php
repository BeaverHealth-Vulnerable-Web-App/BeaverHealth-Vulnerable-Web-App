<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatusMessage extends Component
{
    /**
     * The message to display
     *
     * @var string
     */
    public $message;

    /**
     * The type of message (success/error)
     *
     * @var string
     */
    public $type;

    /**
     * Create a new component instance.
     *
     * @param  string  $message
     * @param  string  $type
     * @return void
     */
    public function __construct(string $message, string $type = 'success')
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.status-message');
    }
}
