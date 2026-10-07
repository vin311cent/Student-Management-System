<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Programme;
use App\Repositories\ProgrammeRepository;
use InvalidArgumentException;

/** Manage programmes and show account info. */
final class SettingsController extends Controller
{
    private ProgrammeRepository $programmes;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->programmes = new ProgrammeRepository(Database::connection());
    }

    public function index(): void
    {
        $this->view('settings/index', [
            'title'      => 'Settings',
            'active'     => 'settings',
            'username'   => Auth::username(),
            'programmes' => $this->programmes->all(),
        ]);
    }

    public function addProgramme(): void
    {
        try {
            $this->programmes->add(new Programme($this->request->string('program_name')));
            $this->flash('success', 'Programme added successfully.');
        } catch (InvalidArgumentException $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/settings');
    }

    public function deleteProgramme(): void
    {
        try {
            $this->programmes->delete($this->request->int('program_id'));
            $this->flash('success', 'Programme deleted. It will no longer appear when registering new students.');
        } catch (InvalidArgumentException $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/settings');
    }
}
