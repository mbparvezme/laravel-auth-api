{{-- 
    Auto-generated Blade form
    Title: Personal Access Tokens
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="tokenable_type" class="block font-medium mb-1">Tokenable Type</label>
    <input type="text" name="tokenable_type" id="tokenable_type" class="border rounded px-3 py-2 w-full" value="{{ old('tokenable_type', '') }}" required>
</div>

<div class="mb-3">
    <label for="tokenable_id" class="block font-medium mb-1">Tokenable Id</label>
    <input type="text" name="tokenable_id" id="tokenable_id" class="border rounded px-3 py-2 w-full" value="{{ old('tokenable_id', '') }}" required>
</div>

<div class="mb-3">
    <label for="name" class="block font-medium mb-1">Name</label>
    <textarea name="name" id="name" class="border rounded px-3 py-2 w-full" required>{{ old('name', '') }}</textarea>
</div>

<div class="mb-3">
    <label for="token" class="block font-medium mb-1">Token</label>
    <input type="text" name="token" id="token" class="border rounded px-3 py-2 w-full" value="{{ old('token', '') }}" required>
</div>

<div class="mb-3">
    <label for="abilities" class="block font-medium mb-1">Abilities</label>
    <textarea name="abilities" id="abilities" class="border rounded px-3 py-2 w-full" >{{ old('abilities', '') }}</textarea>
</div>

<div class="mb-3">
    <label for="attributes" class="block font-medium mb-1">Attributes</label>
    <textarea name="attributes" id="attributes" class="border rounded px-3 py-2 w-full" >{{ old('attributes', '') }}</textarea>
</div>

<div class="mb-3">
    <label for="last_used_at" class="block font-medium mb-1">Last Used At</label>
    <input type="datetime-local" name="last_used_at" id="last_used_at" class="border rounded px-3 py-2 w-full" value="{{ old('last_used_at', '') }}" >
</div>

<div class="mb-3">
    <label for="expires_at" class="block font-medium mb-1">Expires At</label>
    <input type="datetime-local" name="expires_at" id="expires_at" class="border rounded px-3 py-2 w-full" value="{{ old('expires_at', '') }}" >
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>