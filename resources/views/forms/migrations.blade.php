{{-- 
    Auto-generated Blade form
    Title: Migrations
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="migration" class="block font-medium mb-1">Migration</label>
    <input type="text" name="migration" id="migration" class="border rounded px-3 py-2 w-full" value="{{ old('migration', '') }}" required>
</div>

<div class="mb-3">
    <label for="batch" class="block font-medium mb-1">Batch</label>
    <input type="text" name="batch" id="batch" class="border rounded px-3 py-2 w-full" value="{{ old('batch', '') }}" required>
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>