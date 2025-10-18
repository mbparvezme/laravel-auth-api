{{-- 
    Auto-generated Blade form
    Title: Profiles
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="user_id" class="block font-medium mb-1">User Id</label>
    <input type="text" name="user_id" id="user_id" class="border rounded px-3 py-2 w-full" value="{{ old('user_id', '') }}" required>
</div>

<div class="mb-3">
    <label for="profile_picture" class="block font-medium mb-1">Profile Picture</label>
    <input type="file" name="profile_picture" id="profile_picture" class="border rounded px-3 py-2 w-full" value="{{ old('profile_picture', '') }}" >
</div>

<div class="mb-3">
    <label for="mobile" class="block font-medium mb-1">Mobile</label>
    <input type="tel" name="mobile" id="mobile" class="border rounded px-3 py-2 w-full" value="{{ old('mobile', '') }}" >
</div>

<div class="mb-3">
    <label for="address" class="block font-medium mb-1">Address</label>
    <input type="text" name="address" id="address" class="border rounded px-3 py-2 w-full" value="{{ old('address', '') }}" >
</div>

<div class="mb-3">
    <label for="dob" class="block font-medium mb-1">Dob</label>
    <input type="date" name="dob" id="dob" class="border rounded px-3 py-2 w-full" value="{{ old('dob', '') }}" >
</div>

<div class="mb-3">
    <label for="gender" class="block font-medium mb-1">Gender</label>
    <input type="text" name="gender" id="gender" class="border rounded px-3 py-2 w-full" value="{{ old('gender', '') }}" >
</div>

<div class="mb-3">
    <label for="bio" class="block font-medium mb-1">Bio</label>
    <textarea name="bio" id="bio" class="border rounded px-3 py-2 w-full" >{{ old('bio', '') }}</textarea>
</div>

<div class="mb-3">
    <label for="pending_email" class="block font-medium mb-1">Pending Email</label>
    <input type="email" name="pending_email" id="pending_email" class="border rounded px-3 py-2 w-full" value="{{ old('pending_email', '') }}" >
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>