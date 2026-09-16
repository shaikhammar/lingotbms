<?php

namespace App\Modules\Notes\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notes\Models\Note;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Class NotesController
 *
 * @method \Inertia\Response index() Display the notes index page.
 */
class NotesController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('notes/index', [
            'notes' => Note::all(),
        ]);
    }
}
