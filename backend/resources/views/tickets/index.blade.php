docker exec -it laravel_app bash -c 'cat > resources/views/tickets/index.blade.php' << 'EOL'
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-6">My Tickets</h1>
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <a href="{{ route('tickets.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">
                    Buy New Ticket
                </a>

                @if($tickets->isEmpty())
                    <p>No tickets found.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-6 py-3 text-left">Event</th>
                                    <th class="px-6 py-3 text-left">Date</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                    <th class="px-6 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr class="border-b">
                                    <td class="px-6 py-4">{{ $ticket->event_name }}</td>
                                    <td class="px-6 py-4">{{ $ticket->date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $ticket->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                               ($ticket->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('tickets.edit', $ticket) }}" class="text-blue-500 mr-2">Edit</a>
                                        <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500">Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
EOL