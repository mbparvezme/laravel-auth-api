{{-- 
    Auto-generated Blade form
    Title: Failed Jobs
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="uuid" class="block font-medium mb-1">Uuid</label>
    <input type="text" name="uuid" id="uuid" class="border rounded px-3 py-2 w-full" value="{{ old('uuid', '') }}" required>
</div>

<div class="mb-3">
    <label for="connection" class="block font-medium mb-1">Connection</label>
    <textarea name="connection" id="connection" class="border rounded px-3 py-2 w-full" required>{{ old('connection', '') }}</textarea>
</div>

<div class="mb-3">
    <label for="queue" class="block font-medium mb-1">Queue</label>
    <textarea name="queue" id="queue" class="border rounded px-3 py-2 w-full" required>{{ old('queue', '') }}</textarea>
</div>

<div class="mb-3">
    <label for="payload" class="block font-medium mb-1">Payload</label>
    <input type="text" name="payload" id="payload" class="border rounded px-3 py-2 w-full" value="{{ old('payload', '') }}" required>
</div>

<div class="mb-3">
    <label for="exception" class="block font-medium mb-1">Exception</label>
    <input type="text" name="exception" id="exception" class="border rounded px-3 py-2 w-full" value="{{ old('exception', '') }}" required>
</div>

<div class="mb-3">
    <label for="failed_at" class="block font-medium mb-1">Failed At</label>
    <input type="datetime-local" name="failed_at" id="failed_at" class="border rounded px-3 py-2 w-full" value="{{ old('failed_at', 'CURRENT_TIMESTAMP') }}" required>
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>