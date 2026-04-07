<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Auth;

class JoinClass extends Component
{
    public $join_code = '';
    public $showModal = false;

    protected $rules = [
        'join_code' => 'required|string|size:6|exists:classes,join_code',
    ];

    protected $messages = [
        'join_code.exists' => 'Code not found. Please check with your teacher.',
    ];

    public function mount()
    {
        $user = Auth::user();
        
        // Show modal only if user is logged in as a student and has no class assigned.
        // Assuming 'student' role implies they should join a class.
        if ($user && $user->hasRole('student') && is_null($user->class_id)) {
            $this->showModal = true;
        }
    }

    public function joinClass()
    {
        $this->validate();

        $user = Auth::user();

        // Extra check to prevent someone already in a class from joining another
        if (!is_null($user->class_id)) {
            $this->addError('join_code', 'You are already in a class.');
            return;
        }

        $schoolClass = SchoolClass::where('join_code', strtoupper($this->join_code))->first();

        if ($schoolClass) {
            $user->class_id = $schoolClass->id;
            $user->save();

            $this->showModal = false;

            // Optional flash message before redirecting
            session()->flash('message', 'Successfully joined ' . $schoolClass->name . '!');

            // Reload dashboard to reflect new class status
            return redirect()->route('student.dashboard');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.join-class');
    }
}
