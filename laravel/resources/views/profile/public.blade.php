<?php

Route::get('/@{username}', [PublicProfileController::class, 'show']);
?>