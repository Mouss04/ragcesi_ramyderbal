@extends('layouts.app')

@section('content')
    <section class="panel">
        <h1 class="title">Documents</h1>
        <p class="subtitle">Upload PDF files and trigger indexing for the RAG system.</p>

        <div class="grid" style="margin-top: 1rem; align-items: start;">
            <article class="card">
                <h2 style="margin-top: 0;">Upload Document</h2>
                <p class="subtitle">The file is indexed automatically right after upload.</p>
                <form id="upload-form" method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="grid">
                    @csrf
                    <div>
                        <label for="title">Title</label>
                        <input id="title" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div>
                        <label for="file">File (PDF, TXT, MD)</label>
                        <input id="file" name="file" type="file" accept="application/pdf,text/plain,.md" required>
                        <small class="subtitle" style="display:block; margin-top:0.35rem;">Maximum size: 50 MB</small>
                    </div>
                    <button id="upload-button" type="submit" class="btn btn-primary">Upload</button>
                    <div id="upload-progress-wrap" style="display:none;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.35rem;">
                            <small class="subtitle">Uploading and indexing document...</small>
                            <small id="upload-progress-label" class="subtitle">0%</small>
                        </div>
                        <div style="height:10px; border-radius:999px; background:#e5ecee; overflow:hidden;">
                            <div id="upload-progress-bar" style="height:100%; width:0%; background:linear-gradient(90deg, #0f766e 0%, #14b8a6 100%); transition:width 220ms linear;"></div>
                        </div>
                    </div>
                </form>
            </article>
        </div>

        <h2 style="margin-top: 1.3rem;">Uploaded Documents</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Path</th>
                <th>Uploaded At</th>
            </tr>
            </thead>
            <tbody>
            @forelse($documents as $document)
                <tr>
                    <td>{{ $document->id }}</td>
                    <td>{{ $document->title }}</td>
                    <td>{{ $document->file_path }}</td>
                    <td>{{ $document->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No documents uploaded yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <script>
        (() => {
            const form = document.getElementById('upload-form');
            const button = document.getElementById('upload-button');
            const wrap = document.getElementById('upload-progress-wrap');
            const bar = document.getElementById('upload-progress-bar');
            const label = document.getElementById('upload-progress-label');

            if (!form || !button || !wrap || !bar || !label) {
                return;
            }

            let timerId = null;

            form.addEventListener('submit', () => {
                button.disabled = true;
                button.textContent = 'Uploading...';
                wrap.style.display = 'block';

                let progress = 0;
                timerId = window.setInterval(() => {
                    progress = Math.min(progress + Math.max(1, (90 - progress) / 8), 90);
                    const rounded = Math.round(progress);
                    bar.style.width = `${rounded}%`;
                    label.textContent = `${rounded}%`;
                }, 180);
            });

            window.addEventListener('beforeunload', () => {
                if (timerId !== null) {
                    window.clearInterval(timerId);
                }
            });
        })();
    </script>
@endsection
