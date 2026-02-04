<form method="POST" action="{{ $action }}">
    @csrf

    @isset($method)
        @method($method)
    @endisset

    <div class="form-group">
        <label>Name</label>
        <input name="name" value="{{ old('name', $user->name ?? '') }}">
    </div>

    <div class="form-group">
        <label>Email</label>
        <input name="email" value="{{ old('email', $user->email ?? '') }}">
    </div>

    @if(!isset($user))
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password">
    </div>
    @endif

    <div class="form-group">
        <label>Role</label>
        <input name="role" value="{{ old('role', $user->role ?? '') }}">
    </div>

    <button class="btn btn-primary">
        {{ $buttonText }}
    </button>
</form>
