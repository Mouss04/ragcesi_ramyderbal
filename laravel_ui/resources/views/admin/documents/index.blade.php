@extends('layouts.app')

@section('content')
    <section class="panel">
        <h1 class="title">Documents</h1>
        <p class="subtitle">Upload PDF files and trigger indexing for the RAG system.</p>

        <div class="grid cols-2" style="margin-top: 1rem; align-items: start;">
            <article class="card">
                <h2 style="margin-top: 0;">Upload PDF</h2>
                <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="grid">
                    @csrf
                    <div>
                        <label for="title">Title</label>
                        <input id="title" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div>
                        <label for="file">PDF File</label>
                        <input id="file" name="file" type="file" accept="application/pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </article>

            <article class="card">
                <h2 style="margin-top: 0;">RAG Processing</h2>
                <p class="subtitle">Run indexing to include new documents in the answer context.</p>
                <form method="POST" action="{{ route('admin.documents.process') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Process Documents</button>
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
@endsection
