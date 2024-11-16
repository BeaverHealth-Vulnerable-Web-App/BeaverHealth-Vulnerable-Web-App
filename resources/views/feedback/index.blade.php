<!DOCTYPE html>
<html>
<head>
    <title>Patient Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <!-- Comment Form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Add Comment
                    </div>
                    <div class="card-body">
                        <form action="{{ route('feedback.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Comment</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Search Form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Search Comments
                    </div>
                    <div class="card-body">
                        <form action="{{ route('feedback.search') }}" method="GET">
                            <div class="mb-3">
                                <label for="search_name" class="form-label">Search by Name</label>
                                <input type="text" class="form-control" id="search_name" name="search_name" required>
                            </div>
                            <button type="submit" class="btn btn-secondary">Search</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($search_name))
            <div class="text-center mt-4 mb-4">
                <h4>Showing results for: {!! $search_name !!}</h4>
            </div>
            <div class="text-center mt-4 mb-4">
                <a href="{{ route('feedback') }}" class="btn btn-primary">See All Posts</a>
            </div>
        @endif


        <!-- Comments Display -->
        <div class="card mt-4">
            <div class="card-header">
                Comments
            </div>
            <div class="card-body">
                @if($comments->count() > 0)
                    @foreach($comments as $comment)
                        <div class="border-bottom mb-3 pb-3">
                            <h5>{{ $comment->name }}</h5>
                            {!! $comment->comment !!}
                            <br>
                            <small class="text-muted">Posted on: {{ $comment->created_at->format('M d, Y H:i') }}</small>
                        </div>
                    @endforeach
                @else
                    <p>No comments found.</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
