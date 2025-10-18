{{-- 
    Auto-generated Blade form
    Title: Cache Locks
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="key" class="block font-medium mb-1">Key</label>
    <input type="text" name="key" id="key" class="border rounded px-3 py-2 w-full" value="{{ old('key', '') }}" required>
</div>

<div class="mb-3">
    <label for="owner" class="block font-medium mb-1">Owner</label>
    <input type="text" name="owner" id="owner" class="border rounded px-3 py-2 w-full" value="{{ old('owner', '') }}" required>
</div>

<div class="mb-3">
    <label for="expiration" class="block font-medium mb-1">Expiration</label>
    <input type="text" name="expiration" id="expiration" class="border rounded px-3 py-2 w-full" value="{{ old('expiration', '') }}" required>
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>