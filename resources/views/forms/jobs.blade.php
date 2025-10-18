{{-- 
    Auto-generated Blade form
    Title: Jobs
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="queue" class="block font-medium mb-1">Queue</label>
    <input type="text" name="queue" id="queue" class="border rounded px-3 py-2 w-full" value="{{ old('queue', '') }}" required>
</div>

<div class="mb-3">
    <label for="payload" class="block font-medium mb-1">Payload</label>
    <input type="text" name="payload" id="payload" class="border rounded px-3 py-2 w-full" value="{{ old('payload', '') }}" required>
</div>

<div class="mb-3">
    <label for="attempts" class="block font-medium mb-1">Attempts</label>
    <input type="text" name="attempts" id="attempts" class="border rounded px-3 py-2 w-full" value="{{ old('attempts', '') }}" required>
</div>

<div class="mb-3">
    <label for="reserved_at" class="block font-medium mb-1">Reserved At</label>
    <input type="text" name="reserved_at" id="reserved_at" class="border rounded px-3 py-2 w-full" value="{{ old('reserved_at', '') }}" >
</div>

<div class="mb-3">
    <label for="available_at" class="block font-medium mb-1">Available At</label>
    <input type="text" name="available_at" id="available_at" class="border rounded px-3 py-2 w-full" value="{{ old('available_at', '') }}" required>
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>