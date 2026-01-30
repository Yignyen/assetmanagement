<!DOCTYPE html>
<html>
<head>
    <title>Asset Action</title>
</head>
<body>

<h2>Asset Details</h2>

<p><strong>ID:</strong> {{ $asset->id }}</p>
<p><strong>Name:</strong> {{ $asset->name }}</p>
<p><strong>Status:</strong> {{ $asset->status }}</p>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

@if(session('error'))
    <p style="color: red">{{ session('error') }}</p>
@endif

<hr>

{{-- ASSIGN FORM --}}
@if($asset->status === 'available')
    <h3>Assign Asset</h3>

    <form method="POST" action="{{ route('assets.assign', $asset) }}">
        @csrf

        <label>User:</label><br>
        <select name="user_id">
            @foreach($users as $user)
                <option value="{{ $user->id }}">
                    {{ $user->name }}
                </option>
            @endforeach
        </select><br><br>

        <label>Note (optional):</label><br>
        <input type="text" name="note"><br><br>

        <button type="submit">Assign</button>
    </form>
@endif

{{-- UNASSIGN FORM --}}
@if($asset->status === 'assigned')
    <h3>Unassign Asset</h3>

    <p>
        <strong>Assigned To:</strong>
        {{ $asset->assigned->name ?? 'N/A' }}
    </p>

    <form method="POST" action="{{ route('assets.unassign', $asset) }}">
        @csrf

        <label>Note (optional):</label><br>
        <input type="text" name="note"><br><br>

        <button type="submit">Unassign</button>
    </form>
@endif

</body>
</html>
