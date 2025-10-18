{{-- 
    Auto-generated Blade form
    Title: App Logs
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="user" class="block font-medium mb-1">User</label>
    <input type="text" name="user" id="user" class="border rounded px-3 py-2 w-full" value="{{ old('user', '') }}" required>
</div>

<div class="mb-3">
    <label for="action" class="block font-medium mb-1">Action</label>
    <input type="text" name="action" id="action" class="border rounded px-3 py-2 w-full" value="{{ old('action', '') }}" required>
</div>

<div class="mb-3">
    <label for="data" class="block font-medium mb-1">Data</label>
    <textarea name="data" id="data" class="border rounded px-3 py-2 w-full" required>{{ old('data', '') }}</textarea>
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>