@foreach ($users as $user)
<tr>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>
        <form method="POST" action="{{ route('users.destroy', $user->id) }}">
            @csrf
            @method('DELETE')

            <button type="submit"
                onclick="return confirm('Are you sure you want to delete this user?')">
                Delete
            </button>
        </form>
    </td>
</tr>
@endforeach
