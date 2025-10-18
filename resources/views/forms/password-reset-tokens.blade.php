{{-- 
    Auto-generated Blade form
    Title: Password Reset Tokens
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="email" class="block font-medium mb-1">Email</label>
    <input type="email" name="email" id="email" class="border rounded px-3 py-2 w-full" value="{{ old('email', '') }}" required>
</div>

<div class="mb-3">
    <label for="token" class="block font-medium mb-1">Token</label>
    <input type="text" name="token" id="token" class="border rounded px-3 py-2 w-full" value="{{ old('token', '') }}" required>
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>