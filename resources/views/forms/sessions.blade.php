{{-- 
    Auto-generated Blade form
    Title: Sessions
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="user_id" class="block font-medium mb-1">User Id</label>
    <input type="text" name="user_id" id="user_id" class="border rounded px-3 py-2 w-full" value="{{ old('user_id', '') }}" >
</div>

<div class="mb-3">
    <label for="ip_address" class="block font-medium mb-1">Ip Address</label>
    <input type="text" name="ip_address" id="ip_address" class="border rounded px-3 py-2 w-full" value="{{ old('ip_address', '') }}" >
</div>

<div class="mb-3">
    <label for="user_agent" class="block font-medium mb-1">User Agent</label>
    <textarea name="user_agent" id="user_agent" class="border rounded px-3 py-2 w-full" >{{ old('user_agent', '') }}</textarea>
</div>

<div class="mb-3">
    <label for="payload" class="block font-medium mb-1">Payload</label>
    <input type="text" name="payload" id="payload" class="border rounded px-3 py-2 w-full" value="{{ old('payload', '') }}" required>
</div>

<div class="mb-3">
    <label for="last_activity" class="block font-medium mb-1">Last Activity</label>
    <input type="text" name="last_activity" id="last_activity" class="border rounded px-3 py-2 w-full" value="{{ old('last_activity', '') }}" required>
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>