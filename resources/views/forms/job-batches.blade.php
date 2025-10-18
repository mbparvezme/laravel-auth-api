{{-- 
    Auto-generated Blade form
    Title: Job Batches
--}}

<form class="space-y-4" method="POST" action="">
    @csrf

<div class="mb-3">
    <label for="name" class="block font-medium mb-1">Name</label>
    <input type="text" name="name" id="name" class="border rounded px-3 py-2 w-full" value="{{ old('name', '') }}" required>
</div>

<div class="mb-3">
    <label for="total_jobs" class="block font-medium mb-1">Total Jobs</label>
    <input type="text" name="total_jobs" id="total_jobs" class="border rounded px-3 py-2 w-full" value="{{ old('total_jobs', '') }}" required>
</div>

<div class="mb-3">
    <label for="pending_jobs" class="block font-medium mb-1">Pending Jobs</label>
    <input type="text" name="pending_jobs" id="pending_jobs" class="border rounded px-3 py-2 w-full" value="{{ old('pending_jobs', '') }}" required>
</div>

<div class="mb-3">
    <label for="failed_jobs" class="block font-medium mb-1">Failed Jobs</label>
    <input type="text" name="failed_jobs" id="failed_jobs" class="border rounded px-3 py-2 w-full" value="{{ old('failed_jobs', '') }}" required>
</div>

<div class="mb-3">
    <label for="failed_job_ids" class="block font-medium mb-1">Failed Job Ids</label>
    <input type="text" name="failed_job_ids" id="failed_job_ids" class="border rounded px-3 py-2 w-full" value="{{ old('failed_job_ids', '') }}" required>
</div>

<div class="mb-3">
    <label for="options" class="block font-medium mb-1">Options</label>
    <input type="text" name="options" id="options" class="border rounded px-3 py-2 w-full" value="{{ old('options', '') }}" >
</div>

<div class="mb-3">
    <label for="cancelled_at" class="block font-medium mb-1">Cancelled At</label>
    <input type="text" name="cancelled_at" id="cancelled_at" class="border rounded px-3 py-2 w-full" value="{{ old('cancelled_at', '') }}" >
</div>

<div class="mb-3">
    <label for="finished_at" class="block font-medium mb-1">Finished At</label>
    <input type="text" name="finished_at" id="finished_at" class="border rounded px-3 py-2 w-full" value="{{ old('finished_at', '') }}" >
</div>


    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>