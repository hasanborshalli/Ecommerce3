@extends('admin.layout')

@section('title', 'Message from ' . $message->name)
@section('page_title', 'Message from ' . $message->name)
@section('breadcrumb')
    <a href="{{ route('admin.messages.index') }}">Messages</a> › {{ $message->name }}
@endsection

@section('content')

<div style="max-width:680px">

    <div class="admin-card">
        <div class="admin-card-header" style="flex-wrap:wrap;gap:var(--sp-3)">
            <div>
                <div class="admin-card-title">{{ $message->subject ?? 'No subject' }}</div>
                <div style="font-size:var(--text-xs);color:var(--admin-muted);margin-top:2px">
                    Received {{ $message->created_at->format('M d, Y · H:i') }}
                </div>
            </div>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                  onsubmit="return confirm('Delete this message?')">
                @csrf @method('DELETE')
                <button type="submit" class="abtn abtn-danger abtn-sm">Delete</button>
            </form>
        </div>
        <div class="admin-card-body">

            {{-- Sender --}}
            <div style="background:var(--admin-bg);border-radius:var(--radius-lg);padding:var(--sp-4);margin-bottom:var(--sp-5);display:flex;flex-direction:column;gap:var(--sp-2)">
                @php
                $senderFields = [
                    ['Name',  $message->name],
                    ['Email', $message->email],
                    ['Phone', $message->phone ?? '—'],
                ];
                @endphp
                @foreach($senderFields as $field)
                <div style="display:flex;gap:var(--sp-4);font-size:var(--text-sm)">
                    <span style="color:var(--admin-muted);width:60px;flex-shrink:0">{{ $field[0] }}</span>
                    <span style="font-weight:var(--weight-medium)">{{ $field[1] }}</span>
                </div>
                @endforeach
            </div>

            {{-- Message body --}}
            <div style="font-size:var(--text-sm);line-height:var(--leading-relaxed);color:var(--admin-text);white-space:pre-wrap">{{ $message->message }}</div>

            {{-- Reply button --}}
            <div style="margin-top:var(--sp-6);padding-top:var(--sp-5);border-top:1px solid var(--admin-border)">
                <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}"
                   class="abtn abtn-amber">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Reply via Email
                </a>
                <a href="{{ route('admin.messages.index') }}" class="abtn abtn-outline" style="margin-left:var(--sp-2)">
                    ← All Messages
                </a>
            </div>

        </div>
    </div>

</div>

@endsection
