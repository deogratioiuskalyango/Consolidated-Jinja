<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\GovernanceDocument;
use App\Models\GovernanceAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $shareholder = auth()->user()->shareholder;
        $ownerUserId = $shareholder->owner_user_id;
        $data['pageTitle']   = __('Company Documents');
        $data['shareholder'] = $shareholder;

        $query = GovernanceDocument::where('owner_user_id', $ownerUserId)
            ->where('status', '!=', DOC_STATUS_DRAFT)
            ->with('file', 'uploadedBy');

        if ($request->filled('type')) {
            $query->where('document_type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $data['documents'] = $query->orderByDesc('created_at')->paginate(20);
        $data['docTypes']  = GovernanceDocument::where('owner_user_id', $ownerUserId)->distinct()->pluck('document_type')->filter();
        return view('shareholder.documents.index', $data);
    }

    public function show(GovernanceDocument $document)
    {
        $shareholder = auth()->user()->shareholder;
        abort_if($document->owner_user_id !== $shareholder->owner_user_id, 403);

        $document->increment('view_count');
        GovernanceAuditLog::record(AUDIT_DOCUMENT_VIEW, $document, [], 'Viewed document: ' . $document->title);

        $data['pageTitle']   = $document->title;
        $data['document']    = $document->load('file', 'uploadedBy', 'meeting', 'resolution');
        $data['shareholder'] = $shareholder;
        return view('shareholder.documents.show', $data);
    }

    public function download(GovernanceDocument $document)
    {
        $shareholder = auth()->user()->shareholder;
        abort_if($document->owner_user_id !== $shareholder->owner_user_id, 403);

        $document->increment('download_count');
        GovernanceAuditLog::record(AUDIT_DOCUMENT_DOWNLOAD, $document, [], 'Downloaded document: ' . $document->title);

        if ($document->file) {
            $path = $document->file->folder_name . '/' . $document->file->file_name;
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->download($path, $document->title . '.' . pathinfo($document->file->file_name, PATHINFO_EXTENSION));
            }
        }
        return redirect()->back()->with('error', __('File not found.'));
    }
}
