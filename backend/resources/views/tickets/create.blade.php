docker exec -it laravel_app bash -c 'cat > resources/views/tickets/create.blade.php' << 'EOL'
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-6">Purchase Ticket</h1>
                
                <form method="POST" action="{{ route('tickets.store') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="event_name" class="block text-gray-700">Event Name</label>
                        <input type="text" name="event_name" id="event_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @error('event_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="date" class="block text-gray-700">Event Date</label>
                        <input type="date" name="date" id="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @error('date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="quantity" class="block text-gray-700">Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="1" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @error('quantity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('tickets.index') }}" class="text-gray-600 mr-4">Cancel</a>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Purchase</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
EOL