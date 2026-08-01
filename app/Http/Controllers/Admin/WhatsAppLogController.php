<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppLogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = $request->string('status');

        $logsQuery = WhatsAppLog::query()
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->where(function ($inner) use ($search) {
                $inner->where('target', 'like', '%'.$search.'%')
                    ->orWhere('provider', 'like', '%'.$search.'%')
                    ->orWhere('message', 'like', '%'.$search.'%');
            }))
            ->with('registration')
            ->latest();

        return view('admin.whatsapp-logs', [
            'whatsappLogs' => $logsQuery->paginate(15)->withQueryString(),
            'search' => $search,
            'status' => $status,
            'breadcrumbs' => [
                ['label' => 'WhatsApp Log', 'url' => route('dashboard.whatsapp-logs')],
            ],
            'counts' => [
                'all' => WhatsAppLog::query()->count(),
                'pending' => WhatsAppLog::query()->where('status', 'pending')->count(),
                'sent' => WhatsAppLog::query()->where('status', 'sent')->count(),
                'failed' => WhatsAppLog::query()->where('status', 'failed')->count(),
            ],
        ]);
    }
}
