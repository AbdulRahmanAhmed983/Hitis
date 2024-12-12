<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DataTable extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $keys;
    public $removedKeys;
    public $hiddenKeys;
    public $pages;
    public $values;
    public $primaryKey;
    public $edit;
    public $delete;
    public $search;
    public $status;
    public $deleteMessage;

    public function __construct($primaryKey, $status = [], $keys = [], $hiddenKeys = [], $removedKeys = [], $edit = '#',
                                $search = '#', $delete = '#', $pages = null, $deleteMessage = '')
    {
        $this->keys = $keys;
        $this->removedKeys = $removedKeys;
        $this->hiddenKeys = $hiddenKeys;
        $this->primaryKey = $primaryKey;
        $this->pages = $pages;
        $this->values = $this->convertValues($pages, $removedKeys);
        $this->edit = $edit;
        $this->delete = $delete;
        $this->search = $search;
        $this->status = $status;
        $this->deleteMessage = $deleteMessage;
    }

    public function convertValues($values, $removedKeys): array
    {
        $data = [];
        foreach ($values as $value) {
            if (method_exists($value, 'getAttributes')) {
                $data[] = array_diff_key($value->getAttributes(), array_flip($removedKeys));
            } else {
                $data[] = array_diff_key((array)$value, array_flip($removedKeys));
            }
        }
        return $data;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.data-table');
    }
}
