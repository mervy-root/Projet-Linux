docker exec -it laravel_app bash -c 'cat > resources/views/tickets/show.blade.php' << 'EOL'
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <h1 class="text-2xl font-bold">{{ $ticket->event_name }}</h1>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $ticket->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                           ($ticket->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </div>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Event Date:</p>
                        <p class="font-medium">{{ $ticket->date->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Quantity:</p>
                        <p class="font-medium">{{ $ticket->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Purchased On:</p>
                        <p class="font-medium">{{ $ticket->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('tickets.edit', $ticket) }}" 
                       class="bg-blue-500 text-white px-4 py-2 rounded">
                        Edit
                    </a>
                    <form action="{{ route('tickets.destroy', $ticket) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-500 text-white px-4 py-2 rounded">
                            Cancel Ticket
                        </button>
                    </form>
                    <a href="{{ route('tickets.index') }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded">
                        Back to Tickets
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
EOL