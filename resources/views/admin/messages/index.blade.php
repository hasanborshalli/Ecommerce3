@extends('admin.layout')

@section('title', 'Messages')
@section('page_title', 'Messages')
@section('breadcrumb') Admin › Messages @endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>Customer Messages</h1>
        <p>Enquiries submitted through the contact form.</p>
    </div>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width:12px"></th>
                <th>From</th>
                <th>Subject</th>
                <th>Date</th>
                <th style="width:80px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($messages as $msg)
            <tr style="{{ !$msg->is_read ? 'background:var(--blue-light)' : '' }}">
                <td>
                    @if(!$msg->is_read)
                        <div style="width:8px;height:8px;border-radius:var(--radius-full);background:var(--amber);flex-shrink:0"></div>
                    @endif
                </td>
                <td>
                    <div style="font-size:var(--text-sm);font-weight:{{ !$msg->is_read ? 'var(--weight-semibold)' : 'normal' }}">
                        {{ $msg->name }}
                    </div>
                    <div style="font-size:var(--text-xs);color:var(--admin-muted)">{{ $msg->email }}</div>
                </td>
                <td>
                    <a href="{{ route('admin.messages.show', $msg) }}"
                       style="font-size:var(--text-sm);color:var(--admin-accent);font-weight:{{ !$msg->is_read ? 'var(--weight-semibold)' : 'normal' }}">
                        {{ $msg->subject ?? 'No subject' }}
                    </a>
                    <div style="font-size:var(--text-xs);color:var(--admin-muted);margin-top:2px">
                        {{ Str::limit($msg->message, 60) }}
                    </div>
                </td>
                <td style="font-size:var(--text-xs);color:var(--admin-muted);white-space:nowrap">
                    {{ $msg->created_at->format('M d, Y') }}<br>
                    {{ $msg->created_at->format('H:i') }}
                </td>
                <td>
                    <div class="table-actions">
                        <a href="{{ route('admin.messages.show', $msg) }}" class="table-action" title="View">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.messages.destroy', $msg) }}"
                              onsubmit="return confirm('Delete this message?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="table-action delete" title="Delete">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:var(--sp-12);color:var(--admin-muted)">
                    No messages yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="admin-table-footer">
        <span>{{ $messages->total() }} {{ Str::plural('message', $messages->total()) }}</span>
        {{ $messages->links() }}
    </div>
</div>

@endsection
