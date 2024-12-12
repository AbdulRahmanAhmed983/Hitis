<?php

namespace App\View\Components;

use App\Http\Traits\UserTrait;
use Illuminate\View\Component;

class Asidebar extends Component
{
    use UserTrait;

    public $routes;
    public $has_routes;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->routes = $this->getUserRoutes(auth()->id());
        $this->has_routes = in_array(auth()->user()->role, ['student', 'admin', 'owner', 'academic_advising', 'chairman']);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.asidebar');
    }
}
