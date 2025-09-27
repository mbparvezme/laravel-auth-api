<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    echo "<div style='text-align:center;user-select:none;padding-top:12%'>Access forbidden</div>";
});