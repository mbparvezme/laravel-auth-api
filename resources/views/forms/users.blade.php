{{-- 
    Auto-generated Blade form
    Title: Users
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="name" class="block font-medium mb-1">Name</label>
    <input type="text" name="name" id="name" class="border rounded px-3 py-2 w-full" value="{{ old('name', '') }}" required>
</div>

<div class="mb-3">
    <label for="email" class="block font-medium mb-1">Email</label>
    <input type="email" name="email" id="email" class="border rounded px-3 py-2 w-full" value="{{ old('email', '') }}" required>
</div>

<div class="mb-3">
    <label for="email_verified_at" class="block font-medium mb-1">Email Verified At</label>
    <input type="email" name="email_verified_at" id="email_verified_at" class="border rounded px-3 py-2 w-full" value="{{ old('email_verified_at', '') }}" >
</div>

<div class="mb-3">
    <label for="password" class="block font-medium mb-1">Password</label>
    <input type="password" name="password" id="password" class="border rounded px-3 py-2 w-full" value="{{ old('password', '') }}" required>
</div>

<div class="mb-3">
    <label for="remember_token" class="block font-medium mb-1">Remember Token</label>
    <input type="text" name="remember_token" id="remember_token" class="border rounded px-3 py-2 w-full" value="{{ old('remember_token', '') }}" >
</div>

<div class="mb-3">
    <label for="status" class="block font-medium mb-1">Status</label>
    <input type="text" name="status" id="status" class="border rounded px-3 py-2 w-full" value="{{ old('status', '1') }}" required>
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>