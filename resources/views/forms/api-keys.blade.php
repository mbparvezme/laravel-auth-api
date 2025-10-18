{{-- 
    Auto-generated Blade form
    Title: Api Keys
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="user_id" class="block font-medium mb-1">User Id</label>
    <input type="text" name="user_id" id="user_id" class="border rounded px-3 py-2 w-full" value="{{ old('user_id', '') }}" required>
</div>

<div class="mb-3">
    <label for="name" class="block font-medium mb-1">Name</label>
    <input type="text" name="name" id="name" class="border rounded px-3 py-2 w-full" value="{{ old('name', '') }}" required>
</div>

<div class="mb-3">
    <label for="key" class="block font-medium mb-1">Key</label>
    <input type="text" name="key" id="key" class="border rounded px-3 py-2 w-full" value="{{ old('key', '') }}" required>
</div>

<div class="mb-3">
    <label for="secret" class="block font-medium mb-1">Secret</label>
    <input type="text" name="secret" id="secret" class="border rounded px-3 py-2 w-full" value="{{ old('secret', '') }}" required>
</div>

<div class="mb-3">
    <label for="abilities" class="block font-medium mb-1">Abilities</label>
    <textarea name="abilities" id="abilities" class="border rounded px-3 py-2 w-full" >{{ old('abilities', '') }}</textarea>
</div>

<div class="mb-3">
    <label for="expires_at" class="block font-medium mb-1">Expires At</label>
    <input type="datetime-local" name="expires_at" id="expires_at" class="border rounded px-3 py-2 w-full" value="{{ old('expires_at', '') }}" >
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>