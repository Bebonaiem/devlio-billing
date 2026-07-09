¿<?php
namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class Services extends Component
{
    use WithPagination;

    public string $status = '';

    public string $search = '';

    public function render()
    {
        $query = Service::where('user_id', auth()->id())
            ->with(['product', 'plan', 'currency', 'server']);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        $services = $query->latest()->paginate(10);

        return view('livewire.services', compact('services'));
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
